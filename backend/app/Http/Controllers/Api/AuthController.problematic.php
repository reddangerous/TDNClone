<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *    name="Authentication",
 *    description="User authentication endpoints"
 * )
 */
class AuthController
{
    /**
     * Register a new user
     * @OA\Post(
     *    path="/api/v1/auth/register",
     *    summary="Register a new user",
     *    tags={"Authentication"},
     *    @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *            required={"name","email","password"},
     *            @OA\Property(property="name", type="string", example="John Doe"),
     *            @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *            @OA\Property(property="password", type="string", format="password", example="password123")
     *        )
     *    ),
     *    @OA\Response(
     *        response=201,
     *        description="User registered successfully",
     *        @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="User registered successfully"),
     *            @OA\Property(
     *                property="user",
     *                @OA\Property(property="id", type="integer"),
     *                @OA\Property(property="name", type="string"),
     *                @OA\Property(property="email", type="string")
     *            ),
     *            @OA\Property(property="token", type="string", example="token_here")
     *        )
     *    ),
     *    @OA\Response(response=422, description="Validation error")
     * )
     */
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
     * @OA\Post(
     *    path="/api/v1/auth/login",
     *    summary="Login user",
     *    tags={"Authentication"},
     *    @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *            required={"email","password"},
     *            @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *            @OA\Property(property="password", type="string", format="password", example="password123")
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Login successful",
     *        @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Login successful"),
     *            @OA\Property(property="token", type="string")
     *        )
     *    ),
     *    @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Get current user
     * @OA\Get(
     *    path="/api/v1/auth/me",
     *    summary="Get current user",
     *    tags={"Authentication"},
     *    security={{"sanctum":{}}},
     *    @OA\Response(
     *        response=200,
     *        description="Current user",
     *        @OA\JsonContent(
     *            @OA\Property(property="id", type="integer"),
     *            @OA\Property(property="name", type="string"),
     *            @OA\Property(property="email", type="string")
     *        )
     *    )
     * )
     */
    public function getCurrentUser(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Logout user
     * @OA\Post(
     *    path="/api/v1/auth/logout",
     *    summary="Logout user",
     *    tags={"Authentication"},
     *    security={{"sanctum":{}}},
     *    @OA\Response(response=200, description="Logged out successfully")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
