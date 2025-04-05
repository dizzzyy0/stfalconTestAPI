<?php
declare(strict_types=1);

namespace App\Controller;

use App\Enum\Currencies;
use App\Types\Price;
use App\Services\CurrencyConvertService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

class CurrencyConverterController extends AbstractController
{
    public function __construct(
        private readonly CurrencyConvertService $currencyConvertService,
    )
    {}

    #[Route('api/currency/convert', name: 'convert_currency', methods: ['POST'])]
    #[OA\Post(
        description: "Converts an amount from one currency to another.",
        summary: "Convert currency",
        requestBody: new OA\RequestBody(
            description: "Currency conversion parameters",
            required: true,
            content: new OA\JsonContent(
                required: ["fromCurrency", "toCurrency", "amount"],
                properties: [
                    new OA\Property(property: "fromCurrency", type: "string", enum: ["USD", "EUR", "UAH"], example: "USD"),
                    new OA\Property(property: "toCurrency", type: "string", enum: ["USD", "EUR", "UAH"], example: "EUR"),
                    new OA\Property(property: "amount", type: "number", format: "float", example: 100.50)
                ]
            )
        ),
        tags: ["Currency"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Currency converted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "fromCurrency", type: "string", example: "USD"),
                        new OA\Property(property: "toCurrency", type: "string", example: "EUR"),
                        new OA\Property(property: "originalAmount", type: "number", format: "float", example: 100.50),
                        new OA\Property(property: "convertedAmount", type: "number", format: "float", example: 93.47),
                        new OA\Property(property: "exchangeRate", type: "number", format: "float", example: 0.93)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid currency or amount")
        ]
    )]
    public function convertCurrency(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['fromCurrency']) || !isset($data['toCurrency']) || !isset($data['amount'])) {
            return new JsonResponse(
                ['error' => 'Missing required parameters: fromCurrency, toCurrency, amount'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $fromCurrency = Currencies::from(strtolower($data['fromCurrency']));
            $toCurrency = Currencies::from(strtolower($data['toCurrency']));
            $amount = (float) $data['amount'];

            if ($amount < 0) {
                return new JsonResponse(
                    ['error' => 'Amount must be a positive number'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $convertedAmount = $this->currencyConvertService->convertCurrency($fromCurrency, $toCurrency, $amount);
            $exchangeRate = $convertedAmount / $amount;

            return new JsonResponse([
                'fromCurrency' => $fromCurrency->value,
                'toCurrency' => $toCurrency->value,
                'originalAmount' => $amount,
                'convertedAmount' => $convertedAmount,
                'exchangeRate' => $fromCurrency === $toCurrency ? 1.0 : $exchangeRate
            ]);

        } catch (\ValueError $e) {
            return new JsonResponse(
                ['error' => 'Invalid currency provided'],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'An error occurred during currency conversion'],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('api/price/convert', name: 'convert_price', methods: ['POST'])]
    #[OA\Post(
        description: "Converts a price object from one currency to another.",
        summary: "Convert price object",
        requestBody: new OA\RequestBody(
            description: "Price conversion parameters",
            required: true,
            content: new OA\JsonContent(
                required: ["price", "toCurrency"],
                properties: [
                    new OA\Property(property: "price", type: "object", properties: [
                        new OA\Property(property: "amount", type: "number", format: "float", example: 100.50),
                        new OA\Property(property: "currency", type: "string", enum: ["USD", "EUR", "UAH"], example: "USD")
                    ]),
                    new OA\Property(property: "toCurrency", type: "string", enum: ["USD", "EUR", "UAH"], example: "EUR")
                ]
            )
        ),
        tags: ["Currency"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Price converted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "originalPrice", type: "object", properties: [
                            new OA\Property(property: "amount", type: "number", format: "float", example: 100.50),
                            new OA\Property(property: "currency", type: "string", example: "USD")
                        ]),
                        new OA\Property(property: "convertedPrice", type: "object", properties: [
                            new OA\Property(property: "amount", type: "number", format: "float", example: 93.47),
                            new OA\Property(property: "currency", type: "string", example: "EUR")
                        ])
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid price object or currency")
        ]
    )]
    public function convertPrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['price']) || !isset($data['toCurrency'])) {
            return new JsonResponse(
                ['error' => 'Missing required parameters: price, toCurrency'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $priceData = $data['price'];

            if (!isset($priceData['amount']) || !isset($priceData['currency'])) {
                return new JsonResponse(
                    ['error' => 'Price object must contain amount and currency'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $amount = (float) $priceData['amount'];
            $currency = Currencies::from(strtolower($priceData['currency']));
            $toCurrency = Currencies::from(strtolower($data['toCurrency']));

            $price = new Price();
            $price->setAmount($amount);
            $price->setCurrency($currency);

            $convertedPrice = $this->currencyConvertService->convertPrice($price, $toCurrency);

            return new JsonResponse([
                'originalPrice' => [
                    'amount' => $price->getAmount(),
                    'currency' => $price->getCurrency()->value
                ],
                'convertedPrice' => [
                    'amount' => $convertedPrice->getAmount(),
                    'currency' => $convertedPrice->getCurrency()->value
                ]
            ]);

        } catch (\ValueError $e) {
            return new JsonResponse(
                ['error' => 'Invalid currency provided'],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'An error occurred during price conversion'],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('api/exchange-rates', name: 'get_exchange_rates', methods: ['GET'])]
    #[OA\Get(
        description: "Retrieves all available exchange rates.",
        summary: "Get all exchange rates",
        tags: ["Currency"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Exchange rates retrieved successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "USD_UAH", type: "number", format: "float", example: 41.45),
                        new OA\Property(property: "USD_EUR", type: "number", format: "float", example: 0.93),
                        new OA\Property(property: "EUR_UAH", type: "number", format: "float", example: 44.74),
                        new OA\Property(property: "EUR_USD", type: "number", format: "float", example: 1.08),
                        new OA\Property(property: "UAH_USD", type: "number", format: "float", example: 0.024),
                        new OA\Property(property: "UAH_EUR", type: "number", format: "float", example: 0.022)
                    ]
                )
            )
        ]
    )]
    public function getExchangeRates(): JsonResponse
    {
        $rates = [];

        foreach (Currencies::cases() as $fromCurrency) {
            foreach (Currencies::cases() as $toCurrency) {
                if ($fromCurrency !== $toCurrency) {
                    $key = $fromCurrency->value . '_' . $toCurrency->value;
                    $rates[$key] = $this->currencyConvertService->convertCurrency($fromCurrency, $toCurrency, 1.0);
                }
            }
        }

        return new JsonResponse($rates);
    }
}
