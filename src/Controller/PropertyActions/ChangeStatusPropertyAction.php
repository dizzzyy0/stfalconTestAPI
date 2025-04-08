<?php
declare(strict_types=1);

namespace App\Controller\PropertyActions;

use App\DTO\Property\ChangeStatusDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

class ChangeStatusPropertyAction extends PropertyAction
{
    #[Route('/api/properties/{id}/status', name: 'property_change_status', methods: ['PATCH'])]
    #[OA\Patch(
        description: "Changes the status of a property.",
        summary: "Change property status",
        requestBody: new OA\RequestBody(
            description: "New status for the property",
            required: true,
            content: new OA\JsonContent(ref: new Model(type: ChangeStatusDTO::class))
        ),
        tags: ["Property"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'UUID of the property to update status',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Property status updated successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "string", format: "uuid", example: "550e8400-e29b-41d4-a716-446655440003"),
                        new OA\Property(property: "status", properties: [
                            new OA\Property(property: "id", type: "string", example: "available"),
                            new OA\Property(property: "name", type: "string", example: "Available")
                        ], type: "object")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid status or UUID"),
            new OA\Response(response: 404, description: "Not Found - Property with the specified ID not found")
        ]
    )]
    public function changePropertyStatus(
        #[MapRequestPayload] ChangeStatusDTO $changeStatusDTO,
        Uuid $id,
    ): JsonResponse {
        try {
            $updatedProperty = $this->propertyService->changePropertyStatus($id, $changeStatusDTO->status);

            return new JsonResponse([
                'id' => $updatedProperty->getId(),
                'status' => [
                    'id' => $updatedProperty->getStatus()->value,
                    'name' => $updatedProperty->getStatus()->getName(),
                ]
            ], Response::HTTP_OK);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
