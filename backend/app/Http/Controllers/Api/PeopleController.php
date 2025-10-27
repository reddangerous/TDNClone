<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *    title="Tinder Clone API",
 *    version="1.0.0",
 *    description="API for a Tinder-like dating application"
 * )
 * @OA\Server(
 *    url="http://192.168.2.53:8000/api/v1",
 *    description="Development Server"
 * )
 */
class PeopleController extends Controller
{
    /**
     * @OA\Get(
     *    path="/people",
     *    summary="Get list of recommended people",
     *    description="Returns a paginated list of all people",
     *    operationId="getPeople",
     *    tags={"People"},
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
     *                @OA\Property(property="name", type="string"),
     *                @OA\Property(property="age", type="integer"),
     *                @OA\Property(property="location", type="string"),
     *                @OA\Property(property="bio", type="string"),
     *                @OA\Property(property="pictures", type="array", @OA\Items(type="string")),
     *                @OA\Property(property="likes_count", type="integer"),
     *                @OA\Property(property="dislikes_count", type="integer")
     *            )),
     *            @OA\Property(property="current_page", type="integer"),
     *            @OA\Property(property="total", type="integer"),
     *            @OA\Property(property="per_page", type="integer")
     *        )
     *    ),
     *    @OA\Response(
     *        response=500,
     *        description="Server error"
     *    )
     * )
     */
    public function index()
    {
        $people = Person::paginate(10);
        return response()->json($people);
    }

    /**
     * @OA\Get(
     *    path="/people/{id}",
     *    summary="Get a specific person by ID",
     *    description="Returns details of a single person",
     *    operationId="getPersonById",
     *    tags={"People"},
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        description="Person ID",
     *        required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Successful response",
     *        @OA\JsonContent(
     *            @OA\Property(property="id", type="integer"),
     *            @OA\Property(property="name", type="string"),
     *            @OA\Property(property="age", type="integer"),
     *            @OA\Property(property="location", type="string"),
     *            @OA\Property(property="bio", type="string"),
     *            @OA\Property(property="pictures", type="array", @OA\Items(type="string")),
     *            @OA\Property(property="likes_count", type="integer"),
     *            @OA\Property(property="dislikes_count", type="integer")
     *        )
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Person not found"
     *    )
     * )
     */
    public function show(Person $person)
    {
        return response()->json($person);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
