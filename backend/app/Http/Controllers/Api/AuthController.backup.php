<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController
{
    /**
     * Register a new user
     */
    #[OA\Post(
        path: "/api/v1/auth/register",
        summary: "Register a new user",
        description: "Create a new user account",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    'name' => new OA\Property(type: 'string', example: 'John Doe'),
                    'email' => new OA\Property(type: 'string', format: 'email', example: 'john@example.com'),
                    'password' => new OA\Property(type: 'string', format: 'password', example: 'password123'),
                    'password_confirmation' => new OA\Property(type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'User created successfully',
                content: new OA\JsonContent(
                    properties: [
                        'message' => new OA\Property(type: 'string', example: 'User registered successfully'),
                        'user' => new OA\Property(
                            properties: [
                                'id' => new OA\Property(type: 'integer'),
                                'name' => new OA\Property(type: 'string'),
                                'email' => new OA\Property(type: 'string'),
                            ]
                        ),
                        'token' => new OA\Property(type: 'string', example: 'Bearer token...'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Create API token using Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     */
    #[OA\Post(
        path: "/api/v1/auth/login",
        summary: "Login user",
        description: "Authenticate user and return API token",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    'email' => new OA\Property(type: 'string', format: 'email', example: 'john@example.com'),
                    'password' => new OA\Property(type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        'message' => new OA\Property(type: 'string', example: 'Login successful'),
                        'user' => new OA\Property(
                            properties: [
                                'id' => new OA\Property(type: 'integer'),
                                'name' => new OA\Property(type: 'string'),
                                'email' => new OA\Property(type: 'string'),
                            ]
                        ),
                        'token' => new OA\Property(type: 'string', example: 'Bearer token...'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid credentials'),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create API token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * Get current authenticated user
     */
    #[OA\Get(
        path: "/api/v1/auth/user",
        summary: "Get current user",
        description: "Get the authenticated user details",
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Current user data',
                content: new OA\JsonContent(
                    properties: [
                        'id' => new OA\Property(type: 'integer'),
                        'name' => new OA\Property(type: 'string'),
                        'email' => new OA\Property(type: 'string'),
                        'created_at' => new OA\Property(type: 'string', format: 'date-time'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Logout user
     */
    #[OA\Post(
        path: "/api/v1/auth/logout",
        summary: "Logout user",
        description: "Invalidate the current API token",
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Logout successful'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }
}
