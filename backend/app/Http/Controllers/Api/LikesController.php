<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendLikeThresholdNotification;
use App\Models\Like;
use App\Models\Person;
use Illuminate\Http\Request;

class LikesController extends Controller
{
    /**
     * @OA\Post(
     *    path="/likes/like",
     *    summary="Like a person",
     *    description="Record that a user likes a person",
     *    operationId="likePerson",
     *    tags={"Likes"},
     *    @OA\RequestBody(
     *        required=true,
     *        description="User and person IDs",
     *        @OA\JsonContent(
     *            @OA\Property(property="user_id", type="integer", example=1),
     *            @OA\Property(property="person_id", type="integer", example=1)
     *        )
     *    ),
     *    @OA\Response(
     *        response=201,
     *        description="Person liked successfully",
     *        @OA\JsonContent(
     *            @OA\Property(property="message", type="string"),
     *            @OA\Property(property="like", type="object")
     *        )
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Validation error"
     *    )
     * )
     */
    public function like(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:people,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $like = Like::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'person_id' => $request->person_id,
            ],
            [
                'is_liked' => true,
            ]
        );

        $person = Person::find($request->person_id);
        if ($like->wasRecentlyCreated) {
            $person->increment('likes_count');

            // Check if like count reached 50
            if ($person->likes_count >= 50) {
                SendLikeThresholdNotification::dispatch($person);
            }
        }

        return response()->json([
            'message' => 'Person liked successfully',
            'like' => $like,
        ], 201);
    }

    /**
     * @OA\Post(
     *    path="/likes/dislike",
     *    summary="Dislike a person",
     *    description="Record that a user dislikes a person",
     *    operationId="dislikePerson",
     *    tags={"Likes"},
     *    @OA\RequestBody(
     *        required=true,
     *        description="User and person IDs",
     *        @OA\JsonContent(
     *            @OA\Property(property="user_id", type="integer", example=1),
     *            @OA\Property(property="person_id", type="integer", example=1)
     *        )
     *    ),
     *    @OA\Response(
     *        response=201,
     *        description="Person disliked successfully",
     *        @OA\JsonContent(
     *            @OA\Property(property="message", type="string"),
     *            @OA\Property(property="like", type="object")
     *        )
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Validation error"
     *    )
     * )
     */
    public function dislike(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:people,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $like = Like::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'person_id' => $request->person_id,
            ],
            [
                'is_liked' => false,
            ]
        );

        $person = Person::find($request->person_id);
        if ($like->wasRecentlyCreated) {
            $person->increment('dislikes_count');
        }

        return response()->json([
            'message' => 'Person disliked successfully',
            'like' => $like,
        ], 201);
    }

    /**
     * @OA\Get(
     *    path="/likes/liked-people",
     *    summary="Get liked people",
     *    description="Returns a paginated list of people liked by the user",
     *    operationId="getLikedPeople",
     *    tags={"Likes"},
     *    @OA\Parameter(
     *        name="user_id",
     *        in="query",
     *        description="User ID",
     *        required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter(
     *        name="page",
     *        in="query",
     *        description="Page number",
     *        required=false,
     *        @OA\Schema(type="integer", default=1)
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Successful response",
     *        @OA\JsonContent(
     *            @OA\Property(property="data", type="array", @OA\Items(
     *                @OA\Property(property="id", type="integer"),
     *                @OA\Property(property="user_id", type="integer"),
     *                @OA\Property(property="person_id", type="integer"),
     *                @OA\Property(property="person", type="object",
     *                    @OA\Property(property="id", type="integer"),
     *                    @OA\Property(property="name", type="string"),
     *                    @OA\Property(property="age", type="integer"),
     *                    @OA\Property(property="location", type="string"),
     *                    @OA\Property(property="bio", type="string"),
     *                    @OA\Property(property="pictures", type="array", @OA\Items(type="string"))
     *                )
     *            )),
     *            @OA\Property(property="current_page", type="integer"),
     *            @OA\Property(property="total", type="integer")
     *        )
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Validation error"
     *    )
     * )
     */
    public function likedPeople(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $likedPeople = Like::where('user_id', $request->user_id)
            ->where('is_liked', true)
            ->with('person')
            ->paginate(10);

        return response()->json($likedPeople);
    }
}
