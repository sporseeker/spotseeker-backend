<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\VenueService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    use ApiResponse;

    private VenueService $venueRepository;

    public function __construct(VenueService $venueRepository)
    {
        $this->venueRepository = $venueRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/venues",
     *     summary="Create Venue",
     *     description="Create a new venue (Admin only)",
     *     tags={"Venues"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","address","city","capacity"},
     *             @OA\Property(property="name", type="string", example="Grand Concert Hall"),
     *             @OA\Property(property="address", type="string", example="123 Main Street, Colombo"),
     *             @OA\Property(property="city", type="string", example="Colombo"),
     *             @OA\Property(property="capacity", type="integer", example=1000),
     *             @OA\Property(property="description", type="string", example="A beautiful concert hall with excellent acoustics"),
     *             @OA\Property(property="location_url", type="string", example="https://maps.google.com/...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Venue created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Venue created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Grand Concert Hall"),
     *                 @OA\Property(property="address", type="string", example="123 Main Street, Colombo"),
     *                 @OA\Property(property="capacity", type="integer", example=1000)
     *             ),
     *             @OA\Property(property="errors", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(Request $request) {
        $response = $this->venueRepository->createVenue($request);
        return $this->generateResponse($response);
    }

    /**
     * @OA\Get(
     *     path="/api/venues",
     *     summary="Get All Venues",
     *     description="Retrieve all venues (Admin only)",
     *     tags={"Venues"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Venues retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Venues retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Grand Concert Hall"),
     *                 @OA\Property(property="address", type="string", example="123 Main Street, Colombo"),
     *                 @OA\Property(property="capacity", type="integer", example=1000),
     *                 @OA\Property(property="location_url", type="string", example="https://maps.google.com/...")
     *             )),
     *             @OA\Property(property="errors", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index() {
        $response = $this->venueRepository->getAllVenues();
        return $this->generateResponse($response);
    }

    public function destroy($id)
    {
        $response = $this->venueRepository->removeVenue($id);
        return $this->generateResponse($response);
    }

    public function show($id)
    {
        $response = $this->venueRepository->getVenue($id);
        return $this->generateResponse($response);
    }

    public function update(Request $request, $id)
    {
        $response = $this->venueRepository->updateVenue($request, $id);
        return $this->generateResponse($response);
    }
}
