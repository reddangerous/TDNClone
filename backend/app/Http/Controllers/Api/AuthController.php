<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController
{
    /**
     * @OA\Post(
     *    path="/api/v1/auth/register",
     *    operationId="register",
     *    tags={"Authentication"},
     *    summary="Register a new user",
     *    @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *            required={"name","email","password","password_confirmation"},
     *            @OA\Property(property="name", type="string", maxLength=255),
     *            @OA\Property(property="email", type="string", format="email"),
     *            @OA\Property(property="password", type="string", format="password", minLength=8),
     *            @OA\Property(property="password_confirmation", type="string", format="password")
     *        )
     *    ),
     *    @OA\Response(
     *        response=201,
     *        description="User registered successfully"
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Validation error"
     *    )
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
     * @OA\Post(
     *    path="/api/v1/auth/login",
     *    operationId="login",
     *    tags={"Authentication"},
     *    summary="Login user",
     *    @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *            required={"email","password"},
     *            @OA\Property(property="email", type="string", format="email"),
     *            @OA\Property(property="password", type="string", format="password")
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Login successful"
     *    ),
     *    @OA\Response(
     *        response=401,
     *        description="Invalid credentials"
     *    )
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
     * @OA\Get(
     *    path="/api/v1/auth/me",
     *    operationId="getCurrentUser",
     *    tags={"Authentication"},
     *    summary="Get current user",
     *    security={{"sanctum":{}}},
     *    @OA\Response(
     *        response=200,
     *        description="Current user"
     *    )
     * )
     */
    public function getCurrentUser(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * @OA\Post(
     *    path="/api/v1/auth/logout",
     *    operationId="logout",
     *    tags={"Authentication"},
     *    summary="Logout user",
     *    security={{"sanctum":{}}},
     *    @OA\Response(
     *        response=200,
     *        description="Logged out successfully"
     *    )
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
