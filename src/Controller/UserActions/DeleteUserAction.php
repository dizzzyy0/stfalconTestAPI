<?php
declare(strict_types=1);

namespace App\Controller\UserActions;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

class DeleteUserAction extends UserAction
{
    #[Route("/api/users/{id}", name: "api_delete_user", methods: ["DELETE"])]
    #[OA\Delete(
        description: "Deletes a user account with the specified ID.",
        summary: "Delete a user",
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The UUID of the user to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: 'f47ac10b-58cc-4372-a567-0e02b2c3d479')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "User successfully deleted"
            ),
            new OA\Response(
                response: 404,
                description: "Not Found - User with the specified ID not found"
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden - User does not have permission to delete users"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized - JWT token missing or invalid"
            )
        ]
    )]
    public function deleteUser(Uuid $id): JsonResponse
    {
        $this->userService->deleteUser($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
