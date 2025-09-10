<?php

namespace App\Http\Controllers\api;

/**
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     @OA\Property(property="status", type="boolean", description="Request success status"),
 *     @OA\Property(property="code", type="integer", description="HTTP status code"),
 *     @OA\Property(property="message", type="string", description="Response message"),
 *     @OA\Property(property="data", type="object", description="Response data"),
 *     @OA\Property(property="errors", type="array", @OA\Items(), description="Array of errors if any")
 * )
 * 
 * @OA\Schema(
 *     schema="Event",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="Event ID"),
 *     @OA\Property(property="uid", type="string", description="Event unique identifier"),
 *     @OA\Property(property="name", type="string", description="Event name"),
 *     @OA\Property(property="description", type="string", description="Event description"),
 *     @OA\Property(property="type", type="string", enum={"edm","concert","festival","conference"}, description="Event type"),
 *     @OA\Property(property="sub_type", type="string", enum={"INDOOR","OUTDOOR"}, description="Event sub type"),
 *     @OA\Property(property="organizer", type="string", description="Event organizer"),
 *     @OA\Property(property="start_date", type="string", format="datetime", description="Event start date and time"),
 *     @OA\Property(property="end_date", type="string", format="datetime", description="Event end date and time"),
 *     @OA\Property(property="status", type="string", enum={"pending","ongoing","completed","cancelled","soldout"}, description="Event status"),
 *     @OA\Property(property="featured", type="boolean", description="Is event featured"),
 *     @OA\Property(property="currency", type="string", description="Currency code"),
 *     @OA\Property(property="venue", ref="#/components/schemas/Venue"),
 *     @OA\Property(property="ticket_packages", type="array", @OA\Items(ref="#/components/schemas/TicketPackage"))
 * )
 * 
 * @OA\Schema(
 *     schema="Venue",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="Venue ID"),
 *     @OA\Property(property="name", type="string", description="Venue name"),
 *     @OA\Property(property="address", type="string", description="Venue address"),
 *     @OA\Property(property="seating_capacity", type="integer", description="Venue capacity"),
 *     @OA\Property(property="location_url", type="string", description="Google Maps URL")
 * )
 * 
 * @OA\Schema(
 *     schema="TicketPackage",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="Package ID"),
 *     @OA\Property(property="name", type="string", description="Package name"),
 *     @OA\Property(property="price", type="string", description="Package price"),
 *     @OA\Property(property="description", type="string", description="Package description"),
 *     @OA\Property(property="sold_out", type="boolean", description="Is package sold out"),
 *     @OA\Property(property="active", type="boolean", description="Is package active"),
 *     @OA\Property(property="max_tickets_can_buy", type="integer", description="Maximum tickets per purchase")
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="User ID"),
 *     @OA\Property(property="name", type="string", description="User full name"),
 *     @OA\Property(property="first_name", type="string", description="User first name"),
 *     @OA\Property(property="last_name", type="string", description="User last name"),
 *     @OA\Property(property="email", type="string", format="email", description="User email"),
 *     @OA\Property(property="phone_no", type="string", description="User phone number"),
 *     @OA\Property(property="status", type="string", enum={"active","inactive","banned"}, description="User status"),
 *     @OA\Property(property="roles", type="array", @OA\Items(type="object"), description="User roles")
 * )
 * 
 * @OA\Schema(
 *     schema="Booking",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="Booking ID"),
 *     @OA\Property(property="booking_reference", type="string", description="Booking reference number"),
 *     @OA\Property(property="event_id", type="integer", description="Event ID"),
 *     @OA\Property(property="package_id", type="integer", description="Ticket package ID"),
 *     @OA\Property(property="quantity", type="integer", description="Number of tickets"),
 *     @OA\Property(property="total_amount", type="number", format="float", description="Total booking amount"),
 *     @OA\Property(property="booking_status", type="string", enum={"pending","confirmed","cancelled","refunded"}, description="Booking status"),
 *     @OA\Property(property="customer_name", type="string", description="Customer name"),
 *     @OA\Property(property="customer_email", type="string", description="Customer email"),
 *     @OA\Property(property="customer_phone", type="string", description="Customer phone"),
 *     @OA\Property(property="payment_method", type="string", description="Payment method used")
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(property="status", type="boolean", example=false),
 *     @OA\Property(property="code", type="integer", example=422),
 *     @OA\Property(property="message", type="string", example="validation failed"),
 *     @OA\Property(property="data", type="array", @OA\Items()),
 *     @OA\Property(property="errors", type="object", description="Field-specific validation errors")
 * )
 * 
 * @OA\Schema(
 *     schema="UnauthorizedError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Unauthenticated.")
 * )
 * 
 * @OA\Schema(
 *     schema="ForbiddenError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Access denied.")
 * )
 */
class SchemaDefinitions
{
    // This class only exists for Swagger schema definitions
}
