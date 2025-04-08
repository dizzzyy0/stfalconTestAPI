<?php
declare(strict_types=1);

namespace App\Controller\UserActions;

use App\Entity\Customer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

class GetDetailsUserAction extends UserAction
{
    #[Route("/api/users/{id}", name: "api_user_details", methods: ["GET"])]
    #[OA\Get(
        description: "Retrieves the profile information for a specific user (Customer, Admin, or Agent) based on their UUID.",
        summary: "Get user details by ID",
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The UUID of the user to retrieve',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: 'f47ac10b-58cc-4372-a567-0e02b2c3d479')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User details retrieved successfully.",
                content: new OA\JsonContent(
                    description: "User profile data. May include specific fields depending on the user type (e.g., Customer).",
                    properties: [
                        new OA\Property(property: "id", type: "string", format:"uuid"),
                        new OA\Property(property: "role", description: "User role", type: "string", enum: ["ROLE_ADMIN", "ROLE_AGENT", "ROLE_CUSTOMER"]),
                        new OA\Property(property: "email", type: "string", format:"email"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "phone", type: "string", nullable: true),
                        new OA\Property(
                            property: "favoriteProperties",
                            description: "List of favorite property IDs (typically for Customers)",
                            type: "array",
                            items: new OA\Items(type: "string", format: "uuid"),
                            nullable: true
                        ),
                    ],
                    type: "object",
                    example: [
                        "id" => "f47ac10b-58cc-4372-a567-0e02b2c3d479",
                        "role" => "ROLE_CUSTOMER",
                        "email" => "customer@example.com",
                        "name" => "John Doe",
                        "phone" => "+1234567890",
                        "favoriteProperties" => [
                            "prop-uuid-1",
                            "prop-uuid-2"
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Not Found - User with the specified ID was not found or the provided ID format is invalid."
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized - Authentication token missing or invalid."
            ),
        ]
    )]
    public function getUserDetails(Uuid $id): JsonResponse
    {
        $user = $this->userService->getById($id);
        if($user instanceof Customer) {
            return new JsonResponse(
                $this->userPresenter->presentCustomer($user),
                Response::HTTP_OK,
                []
            );
        }
        return new JsonResponse(
            $this->userPresenter->present($user),
            Response::HTTP_OK,
            [],
        );
    }
}
