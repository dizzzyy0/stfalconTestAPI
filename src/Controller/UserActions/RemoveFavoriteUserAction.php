<?php
declare(strict_types=1);

namespace App\Controller\UserActions;

use App\DTO\User\FavoriteDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

class RemoveFavoriteUserAction extends UserAction
{
    #[Route("/api/users/{userId}/favorites/{propertyId}/", name: "api_remove_user_favorite", methods: ["DELETE"])]
    #[OA\Patch(
        description: "Removes a property, identified by its ID in the request body, from the specified user's list of favorite properties.",
        summary: "Remove property from user's favorites",
        tags: ["User Favorites"],
        parameters: [
            new OA\Parameter(
                name: 'userId',
                description: 'The UUID of the user whose favorites are being modified',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: 'f47ac10b-58cc-4372-a567-0e02b2c3d479')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Property successfully removed from favorites. Returns the updated user profile.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "userId", type: "string", format:"uuid"),
                        new OA\Property(property: "email", type: "string", format:"email"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "phone", type: "string"),
                        new OA\Property(
                            property: "favoriteProperties",
                            description: "List of IDs of the user's favorite properties",
                            type: "array",
                            items: new OA\Items(type: "string", format: "uuid")
                        ),
                    ],
                    type: "object",
                )
            ),
            new OA\Response(
                response: 400,
                description: "Bad Request - Invalid UUID format or missing/invalid data in request body."
            ),
            new OA\Response(
                response: 404,
                description: "Not Found - User or Property not found, or property was not in the user's favorites."
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized - Authentication required."
            ),
        ]
    )]
    public function removeCustomersFavorites(Uuid $id, #[MapRequestPayload] FavoriteDTO $favoriteDto): JsonResponse
    {
        return new JsonResponse(
            $this->userPresenter->presentCustomer($this->userService->removeFavoriteProperty($id, $favoriteDto->propertyId)),
            Response::HTTP_OK,
            [],
        );
    }
}
