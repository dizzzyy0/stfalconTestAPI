<?php
declare(strict_types=1);

namespace App\Controller\PropertyActions;

use App\DTO\PaginatedDTO;
use App\DTO\Property\PropertyVisibilityDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class SearchPropertyAction extends PropertyAction
{
    #[Route('api/properties/search', name: 'property_for_user', methods: ['POST'])]
    #[OA\Post(
        description: "Retrieves a paginated list of properties based on user visibility rules and status filters.",
        summary: "Get properties visible to the user (Filtered, Paginated)",
        requestBody: new OA\RequestBody(
            description: "Filters for properties and pagination parameters",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'filters',
                        ref: new Model(type: PropertyVisibilityDTO::class)
                    ),
                    new OA\Property(
                        property: 'pagination',
                        ref: new Model(type: PaginatedDTO::class)
                    ),
                ],
                type: 'object'
            )
        ),
        tags: ["Property"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Paginated list of properties for the user",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'results',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440002'),
                                    new OA\Property(property: 'type', properties: [
                                        new OA\Property(property: 'id', type: 'string', example: 'residential'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Residential Properties')
                                    ], type: 'object'),
                                    new OA\Property(property: 'price', properties: [
                                        new OA\Property(property: 'amount', type: 'integer', example: 150000),
                                        new OA\Property(property: 'currency', properties: [
                                            new OA\Property(property: 'id', type: 'string', example: 'usd'),
                                            new OA\Property(property: 'name', type: 'string', example: 'USD')
                                        ], type: 'object')
                                    ], type: 'object'),
                                    new OA\Property(property: 'location', properties: [
                                        new OA\Property(property: 'address', type: 'string', example: 'Kyiv, Ukraine'),
                                        new OA\Property(property: 'coordinates', properties: [
                                            new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 50.4501),
                                            new OA\Property(property: 'longitude', type: 'number', format: 'float', example: 30.5234)
                                        ], type: 'object')
                                    ], type: 'object'),
                                    new OA\Property(property: 'size', properties: [
                                        new OA\Property(property: 'value', type: 'integer', example: 70),
                                        new OA\Property(property: 'measurement', type: 'string', example: 'm²')
                                    ], type: 'object'),
                                    new OA\Property(property: 'description', type: 'string', example: 'A cozy 2-bedroom apartment...'),
                                    new OA\Property(property: 'status', properties: [
                                        new OA\Property(property: 'id', type: 'string', example: 'available'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Available')
                                    ], type: 'object')
                                ],
                                type: 'object'
                            )
                        ),
                        new OA\Property(
                            property: 'metadata',
                            properties: [
                                new OA\Property(property: 'limit', type: 'integer', example: 10),
                                new OA\Property(property: 'offset', type: 'integer', example: 0),
                                new OA\Property(property: 'total', type: 'integer', example: 50)
                            ],
                            type: 'object'
                        )
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid request body or pagination parameters"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden")
        ]
    )]
    public function propertyForUser(
        #[MapRequestPayload] PropertyVisibilityDTO $forUserDTO,
        #[MapRequestPayload] PaginatedDTO $paginatedDTO,
    ): JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->presentPaginatedProperty(
                $this->propertyService->getPropertiesForUser(
                    $paginatedDTO->offset,
                    $paginatedDTO->limit,
                    $forUserDTO->visibleStatuses
                ),
            ),
            Response::HTTP_OK,
        );
    }
}
