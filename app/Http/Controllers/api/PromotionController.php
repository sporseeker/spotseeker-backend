<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\PromotionService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    use ApiResponse;

    private PromotionService $promotionRepository;

    public function __construct(PromotionService $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/promotions/check",
     *     summary="Check Promotion Code",
     *     description="Validate a promotion code for an event and ticket package",
     *     tags={"Promotions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"promo_code","event_id","package_id","quantity"},
     *             @OA\Property(property="promo_code", type="string", example="DISCOUNT10"),
     *             @OA\Property(property="event_id", type="integer", example=1),
     *             @OA\Property(property="package_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Promotion code validation result",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Promotion code is valid"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="valid", type="boolean", example=true),
     *                 @OA\Property(property="discount_amount", type="number", format="float", example=500.00),
     *                 @OA\Property(property="discount_type", type="string", enum={"percentage","fixed"}, example="percentage"),
     *                 @OA\Property(property="discount_value", type="number", format="float", example=10),
     *                 @OA\Property(property="original_price", type="number", format="float", example=5000.00),
     *                 @OA\Property(property="discounted_price", type="number", format="float", example=4500.00)
     *             ),
     *             @OA\Property(property="errors", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid promotion code",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="code", type="integer", example=400),
     *             @OA\Property(property="message", type="string", example="Invalid promotion code"),
     *             @OA\Property(property="errors", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function checkPromo(Request $request) {
        $response = $this->promotionRepository->checkPromoCodeValidity($request);
        return $this->generateResponse($response);
    }
}
