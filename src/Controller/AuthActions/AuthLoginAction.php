<?php

declare(strict_types=1);

namespace App\Controller\AuthActions;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class AuthLoginAction extends AbstractController
{
    #[Route('/api/auth/login', name: 'api_login', methods: ['POST'])]
    #[OA\Post(
        path: '/api/auth/login',
        description: 'JWT authentication',
        summary: 'Login for user',
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'exampleUser@gmail.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'pass123')
                ]
            )
        ),
        tags: ['Authentication']
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful authentication',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'token', type: 'string', example: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...')
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Invalid email or password')
            ]
        )
    )]
    public function login(): JsonResponse
    {
        return new JsonResponse(['message'=>'success']);
    }
}
