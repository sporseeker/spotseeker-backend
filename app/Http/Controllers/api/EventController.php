<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ApiResponse;

    private EventService $eventRepository;

    public function __construct(EventService $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/events",
     *     summary="Get Events",
     *     description="Retrieve events with optional filtering by status",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="status[]",
     *         in="query",
     *         description="Filter events by status (can pass multiple values)",
     *         required=true,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string", enum={"ongoing","pending","completed","cancelled"})
     *         ),
     *         example={"ongoing","pending"}
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Filter featured events",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limit number of results",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Events retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="events retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="uid", type="string", example="678d3993dd3f3"),
     *                 @OA\Property(property="name", type="string", example="Concert Event"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="type", type="string", example="edm"),
     *                 @OA\Property(property="sub_type", type="string", example="OUTDOOR"),
     *                 @OA\Property(property="organizer", type="string", example="Event Organizer"),
     *                 @OA\Property(property="start_date", type="string", format="datetime", example="2025-04-17 16:00:00"),
     *                 @OA\Property(property="end_date", type="string", format="datetime", example="2025-04-18 06:00:00"),
     *                 @OA\Property(property="status", type="string", example="ongoing"),
     *                 @OA\Property(property="featured", type="boolean", example=true),
     *                 @OA\Property(property="currency", type="string", example="LKR"),
     *                 @OA\Property(property="venue", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Venue Name"),
     *                     @OA\Property(property="location_url", type="string")
     *                 ),
     *                 @OA\Property(property="ticket_packages", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Early Bird"),
     *                     @OA\Property(property="price", type="string", example="5000"),
     *                     @OA\Property(property="sold_out", type="boolean", example=false)
     *                 ))
     *             )),
     *             @OA\Property(property="errors", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - missing required parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="code", type="integer", example=400),
     *             @OA\Property(property="message", type="string", example="something went wrong"),
     *             @OA\Property(property="errors", type="string", example="The status field is required.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $response = $this->eventRepository->getAllEvents($request);
        return $this->generateResponse($response);
    }

    /**
     * @OA\Post(
     *     path="/api/events",
     *     summary="Create Event",
     *     description="Create a new event (Admin only)",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","start_date","start_time","end_date","end_time","venue_id","type","status"},
     *             @OA\Property(property="name", type="string", example="Concert Event"),
     *             @OA\Property(property="description", type="string", example="Amazing concert event"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-12-25"),
     *             @OA\Property(property="start_time", type="string", format="time", example="19:00:00"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-12-25"),
     *             @OA\Property(property="end_time", type="string", format="time", example="23:00:00"),
     *             @OA\Property(property="venue_id", type="integer", example=1),
     *             @OA\Property(property="type", type="string", enum={"edm","concert","festival","conference"}, example="concert"),
     *             @OA\Property(property="sub_type", type="string", enum={"INDOOR","OUTDOOR"}, example="OUTDOOR"),
     *             @OA\Property(property="status", type="string", enum={"pending","ongoing","completed","cancelled"}, example="pending"),
     *             @OA\Property(property="featured", type="boolean", example=false),
     *             @OA\Property(property="currency", type="string", example="LKR"),
     *             @OA\Property(property="event_handling_cost", type="number", format="float", example=500.00),
     *             @OA\Property(property="manager_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Event created successfully"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="errors", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin role required",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Access denied.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $response = $this->eventRepository->createEvent($request);
        return $this->generateResponse($response);
    }

    public function show($id)
    {
        $response = $this->eventRepository->getEvent($id);
        return $this->generateResponse($response);
    }

    public function destroy($id)
    {
        $response = $this->eventRepository->removeEvent($id);
        return $this->generateResponse($response);
    }

    public function updateEvent(Request $request, $id)
    {
        $response = $this->eventRepository->updateEvent($request, $id);
        return $this->generateResponse($response);
    }

    public function getEventStats($id)
    {
        $response = $this->eventRepository->getEventStats($id);
        return $this->generateResponse($response);
    }

    public function getManagerEvents(Request $request)
    {
        $response = $this->eventRepository->getManagerEvents($request);
        return $this->generateResponse($response);
    }

    public function getEventByUID($id)
    {
        $response = $this->eventRepository->getEventByUID($id);
        return $this->generateResponse($response);
    }

    public function sendInvitations(Request $request)
    {
        $response = $this->eventRepository->sendInvitations($request);
        return $this->generateResponse($response);
    }

    public function getInvitation($id)
    {
        $response = $this->eventRepository->getInvitation($id);
        return $this->generateResponse($response);
    }

    public function getInvitations(Request $request, $id)
    {
        $response = $this->eventRepository->getInvitations($request, $id);
        return $this->generateResponse($response);
    }

    public function invitationRSVP(Request $request)
    {
        $response = $this->eventRepository->invitationRSVP($request);
        return $this->generateResponse($response);
    }

    public function getManagerEventById($id) {
        $response = $this->eventRepository->getManagerEventById($id);
        return $this->generateResponse($response);
    }

    public function updateManagerEvent(Request $request, $id) {
        $response = $this->eventRepository->updateManagerEvent($request, $id);
        return $this->generateResponse($response);
    }
}
