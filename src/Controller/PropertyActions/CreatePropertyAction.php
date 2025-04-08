<?php
declare(strict_types=1);

namespace App\Controller\PropertyActions;


use App\DTO\Property\CreatePropertyDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

class CreatePropertyAction extends PropertyAction
{

    #[Route('api/properties/create', name: 'property_create', methods: ['POST'])]
    #[OA\Post(
        description: "Creates a new property listing.",
        summary: "Create a new property",
        requestBody: new OA\RequestBody(
            description: "Data for the new property",
            required: true,
            content: new OA\JsonContent(ref: new Model(type: CreatePropertyDTO::class))
        ),
        tags: ["Property"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Property created successfully",
                content: new OA\JsonContent(
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
                            new OA\Property(property: 'id', type: 'string', example: 'draft'),
                            new OA\Property(property: 'name', type: 'string', example: 'Draft')
                        ], type: 'object')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid data provided"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden - Not allowed to create properties")
        ]
    )]
    public function propertyCreate(
        #[MapRequestPayload] CreatePropertyDTO $createPropertyDTO,
    ): JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->present(
                $this->propertyService->createProperty($createPropertyDTO)
            ),
            Response::HTTP_CREATED,
        );
    }
}
