<?php
declare(strict_types=1);

namespace App\Controller\UserActions;

use App\DTO\PaginatedDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class GetAllUserAction extends UserAction
{
    #[Route("/api/admin/users", name: "api_get_users", methods: ["POST"])]
    //    #[Security(name: 'Bearer')]
    #[OA\Post(
        description: "Retrieves a list of all registered users.",
        summary: "Get list of users",
        requestBody: new OA\RequestBody(
            description: "Pagination parameters",
            required: true,
            content: new OA\JsonContent(ref: new Model(type: PaginatedDTO::class))
        ),
        tags: ["Users"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of users",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "result", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "string", format:"uuid", example: "a1b2c3d4-e5f6-7890-1234-567890abcdef"),
                                new OA\Property(property: "role", type: "string", enum: ["ROLE_ADMIN", "ROLE_AGENT", "ROLE_CUSTOMER"], example: "ROLE_CUSTOMER"),
                                new OA\Property(property: "email", type: "string", format:"email", example: "user@example.com"),
                                new OA\Property(property: "name", type: "string", example: "John Doe"),
                                new OA\Property(property: "phone", type: "string", example: "+380991234567"),
                            ],
                            type: "object"
                        )),
                        new OA\Property(property: "total", type: "integer", example: 50),
                        new OA\Property(property: "offset", type: "integer", example: 0),
                        new OA\Property(property: "limit", type: "integer", example: 10),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden - User does not have permission to view user list"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized - JWT token missing or invalid"
            )
        ]
    )]
    public function getUsers(
        #[MapRequestPayload] PaginatedDTO $metadata
    ): JsonResponse
    {
        return new JsonResponse(
            $this->userPresenter->presentPaginatedUser($this->userService->getAllUsers(
                $metadata->offset,
                $metadata->limit,
            )),
            Response::HTTP_OK,
            [],
        );
    }
}
