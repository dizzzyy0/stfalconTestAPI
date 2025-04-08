<?php
declare(strict_types=1);

namespace App\Controller\UserActions;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

class BlockUserAction extends UserAction
{
    #[Route('/api/users/block', name: "api_users_block", methods: ["POST"])]
    //    #[Security(name: 'Bearer')]
    #[OA\Post(
        description: "Marks a user account as blocked.",
        summary: "Block a user",
        requestBody: new OA\RequestBody(
            description: "ID of the user to block",
            required: true,
            content: new OA\JsonContent(
                required: ["id"],
                properties: [
                    new OA\Property(property: "id", description: "User's unique identifier", type: "string", format:"uuid", example: "a1b2c3d4-e5f6-7890-1234-567890abcdef")
                ]
            )
        ),
        tags: ["Users"],
        responses: [
            new OA\Response(
                response: 204,
                description: "User successfully blocked"
            ),
            new OA\Response(
                response: 400,
                description: "Invalid JSON payload or missing 'id' field"
            ),
            new OA\Response(
                response: 404,
                description: "User with the specified ID not found"
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden - User does not have permission to block users"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized - JWT token missing or invalid"
            )
        ]
    )]
    public function block(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return new JsonResponse(["message" => "Некоректний JSON", "data"=>$data], Response::HTTP_BAD_REQUEST);
        }
        $id = $data["id"];
        $this->userService->blockUser(Uuid::fromString($id));
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
