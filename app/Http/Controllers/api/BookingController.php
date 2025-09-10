<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use App\Services\EventService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use ApiResponse;

    private BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * @OA\Post(
     *     path="/api/bookings",
     *     summary="Create Booking",
     *     description="Create a new ticket booking for an event",
     *     tags={"Bookings"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"event_id","package_id","quantity","customer_name","customer_email","customer_phone"},
     *             @OA\Property(property="event_id", type="integer", example=1),
     *             @OA\Property(property="package_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=2),
     *             @OA\Property(property="customer_name", type="string", example="John Doe"),
     *             @OA\Property(property="customer_email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="customer_phone", type="string", example="0771234567"),
     *             @OA\Property(property="payment_method", type="string", enum={"card","cash","bank_transfer"}, example="card"),
     *             @OA\Property(property="promo_code", type="string", example="DISCOUNT10")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Booking created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="booking_id", type="integer", example=1),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=10000.00),
     *                 @OA\Property(property="payment_url", type="string", example="https://payment.gateway.com/pay/123"),
     *                 @OA\Property(property="booking_reference", type="string", example="BK-2025-001")
     *             ),
     *             @OA\Property(property="errors", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="code", type="integer", example=400),
     *             @OA\Property(property="message", type="string", example="Booking failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $response = $this->bookingService->createBooking($request);
        return $this->generateResponse($response);
    }

    public function updateBooking(Request $request)
    {
        $response = $this->bookingService->updateBooking($request);
        return $this->generateResponse($response);
    }

    public function index(Request $request)
    {
        $response = $this->bookingService->getAllBookings($request);
        return $this->generateResponse($response);
    }

    public function verifyBooking(Request $request)
    {
        $response = $this->bookingService->verifyBooking($request);
        return $this->generateResponse($response);
    }

    public function update(Request $request, $id)
    {
        $response = $this->bookingService->updateBookingData($request, $id);
        return $this->generateResponse($response);
    }

    public function getBooking(Request $request, $id)
    {
        $response = $this->bookingService->getBooking($request, $id);
        return $this->generateResponse($response);
    }

    public function generateETicket($id) {
        $response = $this->bookingService->generateETicket($id);
        return $this->generateResponse($response);
    }

    public function generateSubBookings($id) {
        $response = $this->bookingService->generateSubBookings($id);
        return $this->generateResponse($response);
    }
    
    public function updateStatus(Request $request, $id) {
        $response = $this->bookingService->updateStatus($request, $id);
        return $this->generateResponse($response);
    }
}
