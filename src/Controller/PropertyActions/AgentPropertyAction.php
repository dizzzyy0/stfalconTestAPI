<?php
declare(strict_types=1);

namespace App\Controller\PropertyActions;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

class AgentPropertyAction extends PropertyAction
{
    #[Route('api/agents/{agentId}/properties', name: 'agent_property', methods: ['GET'])]
    #[OA\Get(
        description: "Retrieves a list of properties associated with the specified agent ID.",
        summary: "Get properties for a specific agent",
        tags: ["Property"],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Agent UUID', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of properties for the agent",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440003'),
                            new OA\Property(property: 'type', properties: [
                                new OA\Property(property: 'id', type: 'string', example: 'land'),
                                new OA\Property(property: 'name', type: 'string', example: 'Land Properties')
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
                                new OA\Property(property: 'value', type: 'integer', example: 120),
                                new OA\Property(property: 'measurement', type: 'string', example: 'm²')
                            ], type: 'object'),
                            new OA\Property(property: 'description', type: 'string', example: '120 m² of prime land in Kyiv...'),
                            new OA\Property(property: 'status', properties: [
                                new OA\Property(property: 'id', type: 'string', example: 'draft'),
                                new OA\Property(property: 'name', type: 'string', example: 'Draft')
                            ], type: 'object')
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid Agent UUID format"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden - Not allowed to view this agent's properties"),
            new OA\Response(response: 404, description: "Not Found - Agent with the specified ID not found")
        ]
    )]
    public function agentProperty(Uuid $id): JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->presentList(
                $this->propertyService->getAgentProperties($id)
            ),
            Response::HTTP_OK,
        );
    }
}
