<?php
declare(strict_types=1);

namespace App\Controller\CurrencyConverterActions;


use App\Enum\Currencies;
use App\Services\CurrencyConvertService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class CurrencyConvertAction extends AbstractController
{
    public function __construct(
        private readonly CurrencyConvertService $currencyConvertService,
    )
    {}

    #[Route('api/currencies/convert', name: 'convert_currency', methods: ['POST'])]
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
        tags: ["Currencies"],
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
}
