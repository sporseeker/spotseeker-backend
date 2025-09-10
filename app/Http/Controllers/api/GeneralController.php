<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="General",
 *     description="General API endpoints"
 * )
 */
class GeneralController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/healthcheck",
     *     summary="Health Check",
     *     description="Check if the API is running properly",
     *     tags={"General"},
     *     @OA\Response(
     *         response=200,
     *         description="API is healthy",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="OK")
     *         )
     *     )
     * )
     */
    public function healthCheck()
    {
        return response()->json(['status' => 'OK']);
    }

    /**
     * @OA\Get(
     *     path="/api/search",
     *     summary="Search Events",
     *     description="Search for events by query string",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         required=true,
     *         description="Search query",
     *         @OA\Schema(type="string", example="concert")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Concert Event"),
     *                 @OA\Property(property="organizer", type="string", example="Event Organizer"),
     *                 @OA\Property(property="start_date", type="string", format="datetime", example="2025-04-17 16:00"),
     *                 @OA\Property(property="status", type="string", example="ongoing"),
     *                 @OA\Property(property="venue", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Venue Name")
     *                 ),
     *                 @OA\Property(property="ticket_packages", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Early Bird"),
     *                     @OA\Property(property="price", type="string", example="5000")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - missing query parameter",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Query parameter 'q' is required")
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        $events = Event::search($request->q)->whereIn(
            'status',
            ['ongoing', 'pending', 'soldout']
        )->get();
        $events->load('venue', 'ticket_packages');
        return $events;
    }

    /**
     * @OA\Get(
     *     path="/api/event/{uid}",
     *     summary="Get Event by UID",
     *     description="Get a specific event by its unique identifier",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="uid",
     *         in="path",
     *         required=true,
     *         description="Event unique identifier",
     *         @OA\Schema(type="string", example="678d3993dd3f3")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Event retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="uid", type="string", example="678d3993dd3f3"),
     *                 @OA\Property(property="name", type="string", example="Concert Event"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="start_date", type="string", format="datetime", example="2025-04-17 16:00:00"),
     *                 @OA\Property(property="end_date", type="string", format="datetime", example="2025-04-18 06:00:00"),
     *                 @OA\Property(property="status", type="string", example="ongoing"),
     *                 @OA\Property(property="venue", type="object"),
     *                 @OA\Property(property="ticket_packages", type="array", @OA\Items(type="object"))
     *             ),
     *             @OA\Property(property="errors", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Event not found")
     *         )
     *     )
     * )
     */
    public function getEventByUID($uid)
    {
        // This method would be handled by EventController::getEventByUID
        // This is just for documentation purposes
    }
}
