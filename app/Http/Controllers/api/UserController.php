<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\UnauthorizedException;

class UserController extends Controller
{
    use ApiResponse;

    private UserService $userRepository;

    public function __construct(UserService $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get All Users",
     *     description="Get all users (Admin only)",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of results per page",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Users retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="phone_no", type="string", example="0771234567"),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="object"))
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
    public function index(Request $request) {
        $response = $this->userRepository->getAllUsers($request);
        return $this->generateResponse($response);
    }

    public function store(Request $request) {
        $response = $this->userRepository->storeUser($request);
        return $this->generateResponse($response);
    }

    /**
     * @OA\Get(
     *     path="/api/user/orders",
     *     summary="Get User Orders",
     *     description="Get current user's booking orders",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User orders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="User data retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com")
     *                 ),
     *                 @OA\Property(property="orders", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="booking_reference", type="string", example="BK-2025-001"),
     *                     @OA\Property(property="event_name", type="string", example="Concert Event"),
     *                     @OA\Property(property="total_amount", type="number", format="float", example=5000.00),
     *                     @OA\Property(property="booking_status", type="string", example="confirmed")
     *                 ))
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
    public function getOrders(Request $request) {
        $response = $this->userRepository->getUserData($request);
        return $this->generateResponse($response);
    }
    
    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get User Profile",
     *     description="Get current authenticated user's profile data",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="User data retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="phone_no", type="string", example="0771234567"),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="object"))
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
    public function getUserData(Request $request) {
        $response = $this->userRepository->getUserData($request);
        return $this->generateResponse($response);
    }

    public function verifyAdminUser(Request $request) {
        $user_id = Auth::user()->id;
        $user = User::findOrFail($user_id);
        if (Hash::check($request->input('password'), $user->password))
        {
            if(Auth::user()->hasRole('Admin')) {
                return $this->successResponse("password verified", null);
            } else {
                throw new UnauthorizedException("You are not authorized to perform this action");
            }
        } else {
            throw new UnauthorizedException("You are not authorized to perform this action");
        }
    }

    public function update(Request $request, $id) {
        $response = $this->userRepository->updateUser($request, $id);
        return $this->generateResponse($response);
    }

    public function updateProfile(Request $request) {
        $response = $this->userRepository->updateProfile($request);
        return $this->generateResponse($response);
    }
    
    public function destroy($id) {
        $response = $this->userRepository->deleteUser($id);
        return $this->generateResponse($response);
    }

    public function destroyUserAccount(Request $request) {
        $response = $this->userRepository->deleteUserAccount($request);
        return $this->generateResponse($response);
    }

    public function banUser($id) {
        $response = $this->userRepository->deactivateUser($id);
        return $this->generateResponse($response);
    }

    public function activateUser($id) {
        $response = $this->userRepository->activateUser($id);
        return $this->generateResponse($response);
    }

    public function getManagerCoordinators() {
        $response = $this->userRepository->getManagerCoordinators();
        return $this->generateResponse($response);
    }

    public function createManagerCoordinator(Request $request) {
        $response = $this->userRepository->storeManagerCoordinator($request);
        return $this->generateResponse($response);
    }

    public function deleteManagerCoordinator($id) {
        $response = $this->userRepository->deleteManagerCoordinator($id);
        return $this->generateResponse($response);
    }

    public function deactivateManagerCoordinator($id) {
        $response = $this->userRepository->deactivateManagerCoordinator($id);
        return $this->generateResponse($response);
    }
    
    public function activateManagerCoordinator($id) {
        $response = $this->userRepository->activateManagerCoordinator($id);
        return $this->generateResponse($response);
    }

    public function updateManagerCoordinator(Request $request, $id) {
        $response = $this->userRepository->updateManagerCoordinator($request, $id);
        return $this->generateResponse($response);
    }
}
