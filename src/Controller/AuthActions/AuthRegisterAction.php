<?php
declare(strict_types=1);

namespace App\Controller\AuthActions;

use App\DTO\User\RegisterUserDTO;
use App\Presenters\UserPresenter;
use App\Services\UserService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class AuthRegisterAction extends AbstractController
{
    public function __construct(
        private readonly UserService   $userService,
        private readonly UserPresenter $userPresenter,
    )
    {
    }

    #[Route("/api/auth/register", name: "api_register", methods: ["POST"])]
    #[OA\Post(
        path: "/api/auth/register",
        description: "Creates a new user account based on provided data.",
        summary: "Register a new user",
        security: [],
        requestBody: new OA\RequestBody(
            description: "Data for the new user",
            required: true,
            content: new OA\JsonContent(ref: new Model(type: RegisterUserDto::class))
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 201,
                description: "User successfully registered",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "string", format:"uuid", example: "a1b2c3d4-e5f6-7890-1234-567890abcdef"),
                        new OA\Property(property: "role", type: "string", enum: ["ROLE_ADMIN", "ROLE_AGENT", "ROLE_CUSTOMER"], example: "ROLE_CUSTOMER"),
                        new OA\Property(property: "email", type: "string", format:"email", example: "newuser@example.com"),
                        new OA\Property(property: "name", type: "string", example: "John Doe"),
                        new OA\Property(property: "phone", type: "string", example: "+380991234567"),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation failed (e.g., invalid email format, missing fields, password too short)"
            ),
            new OA\Response(
                response: 409,
                description: "User with this email already exists"
            )
        ]
    )]
    public function register(
        #[MapRequestPayload] RegisterUserDto $registerDto,
    ): JsonResponse
    {
        try {
            $newUser = $this->userService->register($registerDto);
            if(!$newUser->getEmail())
            {
                return new JsonResponse(['message'=>'user already exists'], Response::HTTP_CONFLICT, []);
            }
            return new JsonResponse($this->userPresenter->present($newUser), Response::HTTP_CREATED, []);
        } catch (\Exception $e) {
            return new JsonResponse(['message'=>$e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY, []);
        }
    }
}
