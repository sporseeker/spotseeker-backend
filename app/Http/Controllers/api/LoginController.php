<?php

namespace App\Http\Controllers\api;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class LoginController extends Controller
{

    use ApiResponse;

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User Login",
     *     description="Login user with email and password",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="user@example.com"),
     *                     @OA\Property(property="phone_no", type="string", example="0771234567"),
     *                     @OA\Property(property="roles", type="array", @OA\Items(type="object"))
     *                 ),
     *                 @OA\Property(property="access_token", type="string", example="1|abc123...")
     *             ),
     *             @OA\Property(property="errors", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="message", type="string", example="Authentication failed"),
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
    public function login(Request $request)
    {
        // Validate input fields
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Check if the user has a provider (social login) record
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            // Check if the user has logged in using a social provider
            if (sizeof($user->provider)) {
                return $this->generateResponse((object)[
                    'message' => 'This account is associated with a social login. Please use the social login method.',
                    'status' => false,
                    'code' => 401
                ]);  // Forbidden status for incorrect login method
            }
        }

        // Attempt login with provided credentials
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')]
            ]);
        }

        // Fetch the logged-in user
        $loggedInUser = $request->user();

        // Check if the user's account is suspended
        if ($loggedInUser->status == 0) {
            // Log out the user and invalidate the session
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Return error message for suspended account
            throw ValidationException::withMessages([
                'email' => 'Your account is suspended, please contact SpotSeeker.lk.'
            ]);
        }

        // Assign role and generate an authentication token
        $loggedInUser->role = $loggedInUser->getRoleNames()[0] ?? 'user'; // Default to 'user' if no role
        $loggedInUser->token = $loggedInUser->createToken($loggedInUser->email)->plainTextToken;
        $loggedInUser->verified = $user->hasVerifiedMobile();

        // Return success response with user data
        return $this->successResponse("Login successful", $loggedInUser);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="User Registration",
     *     description="Register a new user account",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation","phone_no","verification_method"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="phone_no", type="string", example="0771234567"),
     *             @OA\Property(property="verification_method", type="string", enum={"sms","email"}, example="sms")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Registration successful! A verification code has been sent to your mobile number."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="phone_no", type="string", example="0771234567"),
     *                 @OA\Property(property="token", type="string", example="1|abc123..."),
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="object"))
     *             ),
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
    public function register(Request $request)
    {
        // Step 1: Validate input data
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_no' => 'required|numeric|digits:10|unique:users,phone_no',
            'verification_method' => 'required|string'
        ];

        // Add role validation if ALLOW_ROLE_ON_REGISTER is enabled (development only)
        if (env('ALLOW_ROLE_ON_REGISTER', false)) {
            $validationRules['role'] = 'sometimes|string|in:Admin,Manager,User,Coordinator';
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return $this->generateResponse((object)['message' => 'validation failed', 'errors' => $validator->errors(), "status" => false, "code" => 422]);
        }

        // Determine role - default to 'User' unless specified and allowed
        $role = 'User';
        if (env('ALLOW_ROLE_ON_REGISTER', false) && $request->has('role')) {
            $role = $request->role;
            // Debug log
            Log::info('Role assignment enabled. Requested role: ' . $role);
        } else {
            // Debug log
            Log::info('Role assignment: ALLOW_ROLE_ON_REGISTER=' . env('ALLOW_ROLE_ON_REGISTER', 'false') . ', has_role=' . ($request->has('role') ? 'true' : 'false'));
        }

        $user = $this->registerUser($request->name, $request->email, $request->password, $request->phone_no, $request->verification_method, "", true, 'credentials', null, $role);

        return $this->generateResponse((object)[
            'message' => 'Registration successful! A verification code has been sent to your mobile number.',
            'data' => $user,
            "status" => true,
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::user()) {
            Auth::user()->tokens()->delete();

            $authObj = (object)[
                "message" => 'logged out successfully',
                "status" => true
            ];

            return $this->generateResponse($authObj);
        }

        $authFailObj = (object)[
            "message" => 'user not found',
            "status" => false,
            "code" => 500
        ];

        return $this->generateResponse($authFailObj);
    }

    protected function socialLogin(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate input
            $request->validate([
                'token' => ['required'],
                'provider' => ['required']
            ]);

            // Authenticate the user using Socialite
            $user = null;
            if ($request->provider == 'google') {
                $user = Socialite::driver('google')->stateless()->userFromToken($request->token);
            } else if ($request->provider == 'apple') {
                $user = Socialite::driver("apple")->stateless()->userFromToken($request->token);
            } else if ($request->provider == 'facebook') {
                $user = Socialite::driver("facebook")->stateless()->userFromToken($request->token);
            } else {
                throw new Exception("Unsupported provider");
            }

            // Check if the user exists in the database
            $loggedInUser = User::where('email', $user->email)->first();

            if (!$loggedInUser) {
                // Register new user if not exists
                $loggedInUser = $this->registerUser($user->name, $user->email, "", null, null, $user->avatar, false, $request->provider, $user, 'User');
            } else {

                $parts = explode(' ', $user->name);
                $first_name = $parts[0];
                $last_name = isset($parts[1]) ? $parts[1] : '';

                $loggedInUser->name = $user->name;
                $loggedInUser->first_name = $first_name;
                $loggedInUser->last_name = $last_name;
                $loggedInUser->profile_photo_path = $user->avatar;

                // Save the user
                $loggedInUser->save();
            }

            $accessToken = $loggedInUser->createToken($loggedInUser->email)->plainTextToken;


            // Prepare authentication data
            $authDataObj = [
                'first_name' => $loggedInUser->first_name,
                'last_name' => $loggedInUser->last_name,
                'email' => $loggedInUser->email,
                'phone_no' => $loggedInUser->phone_no,
                'nic' => $loggedInUser->nic,
                'role' => $loggedInUser->getRoleNames()->first() ?? 'user',
                'token' => $accessToken,
                'profile_photo_url' => $loggedInUser->profile_photo_path,
                'verified' => $loggedInUser->hasVerifiedMobile()
            ];

            // Success response
            $authObj = (object)[
                "message" => 'Authenticated successfully',
                "status" => true,
                "data" => $authDataObj
            ];

            DB::commit();

            return $this->generateResponse($authObj);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            // Error response
            $authObj = (object)[
                "message" => $e->getMessage(),
                "status" => false,
                "code" => 401
            ];

            return $this->generateResponse($authObj);
        }
    }

    public function forgotPassword(Request $request)
    {
        // Validate the incoming email request
        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        // Attempt to send the password reset link
        $status = Password::sendResetLink($validated);

        // Log the status of the reset link
        Log::info('Password reset status: ' . $status);

        // Return a response based on the status
        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse(
                'Reset password email sent successfully.',
                $status
            );
        }

        return $this->errorResponse(
            'Failed to send reset password email.',
            $status,
            400
        );
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->successResponse("password reset successful", $status)
            : $this->errorResponse("password reset failed", $status, 400);
    }

    protected function managerSocialLogin(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'token' => ['required'],
                'provider' => ['required']
            ]);

            // Authenticate the user using Socialite
            $user = null;

            if ($request->provider == 'google') {
                $user = Socialite::driver('google')->stateless()->userFromToken($request->token);
            } else if ($request->provider == 'apple') {
                $user = Socialite::driver("apple")->stateless()->userFromToken($request->token);
            } else {
                throw new Exception("Unsupported provider");
            }

            // Check if the user exists in the database
            $loggedInUser = User::where('email', $user->email)->first();

            if (!$loggedInUser) {
                throw new Exception("User not found");
            } else if ($loggedInUser->status == 0) {

                $authFailObj = (object)[
                    "message" => 'Your Account is suspended, please contact SpotSeeker.lk.',
                    "status" => false,
                    "code" => 401
                ];

                return $this->generateResponse($authFailObj);
            } else if (!$loggedInUser->hasRole([Roles::ADMIN->value, Roles::MANAGER->value, Roles::COORDINATOR->value])) {
                $authFailObj = (object)[
                    "message" => 'Email & Password does not match with our record.',
                    "status" => false,
                    "code" => 403
                ];

                return $this->generateResponse($authFailObj);
            }

            // Prepare authentication data
            $authDataObj = [
                'username' => $loggedInUser->name,
                'email' => $loggedInUser->email,
                'role' => $loggedInUser->getRoleNames()->first() ?? Roles::COORDINATOR->value, // Assuming 'user' as a default role
                'token' => $loggedInUser->createToken($loggedInUser->email)->plainTextToken
            ];

            // Success response
            $authObj = (object)[
                "message" => 'Authenticated successfully',
                "status" => true,
                "data" => $authDataObj
            ];
        } catch (Exception $e) {
            // Error response
            $authObj = (object)[
                "message" => $e->getMessage(),
                "status" => false,
                "code" => 400
            ];
        }

        return $this->generateResponse($authObj);
    }

    public function managerLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($credentials)) {

                $authFailObj = (object)[
                    "message" => 'Email & Password does not match with our record.',
                    "status" => false,
                    "code" => 401
                ];

                return $this->generateResponse($authFailObj);
            }

            if (Auth::user()->status == 0) {

                $authFailObj = (object)[
                    "message" => 'Your Account is suspended, please contact SpotSeeker.lk.',
                    "status" => false,
                    "code" => 401
                ];

                return $this->generateResponse($authFailObj);
            } else if (!Auth::user()->hasRole([Roles::ADMIN->value, Roles::MANAGER->value, Roles::COORDINATOR->value])) {
                $authFailObj = (object)[
                    "message" => 'Email & Password does not match with our record.',
                    "status" => false,
                    "code" => 403
                ];

                return $this->generateResponse($authFailObj);
            }

            $authDataObj = [
                'username' => Auth::user()->name,
                'email' => Auth::user()->email,
                'role' => Auth::user()->getRoleNames()->first() ?? 'user', // Assuming 'user' as a default role
                'token' => Auth::user()->createToken(Auth::user()->email)->plainTextToken
            ];

            $authObj = (object)[
                "message" => 'authenticated successfully',
                "status" => true,
                "data" => $authDataObj
            ];

            return $this->generateResponse($authObj);
        } catch (Exception $e) {
            $authObj = (object)[
                "message" => $e->getMessage(),
                "status" => false,
                "code" => 401
            ];
        }
        return $this->generateResponse($authObj);
    }

    public function managerLogOut(Request $request)
    {
        if (Auth::user()) {
            Auth::user()->tokens()->delete();

            $authObj = (object)[
                "message" => 'logged out successfully',
                "status" => true
            ];

            return $this->generateResponse($authObj);
        }

        $authFailObj = (object)[
            "message" => 'user not found',
            "status" => false,
            "code" => 500
        ];

        return $this->generateResponse($authFailObj);
    }

    private function registerUser($name, $email, $password, $phone_no, $verification_method = null, $profile_photo_path = null, $verify = false, $provider = 'credentials', $socialUser = null, $roleName = 'User')
    {
        $parts = explode(' ', $name);
        $first_name = $parts[0];
        $last_name = isset($parts[1]) ? $parts[1] : '';

        $user = User::create([
            'name' => $name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => Hash::make($password),
            'phone_no' => $phone_no,
            'verification_method' => $verification_method,
            'profile_photo_path' => $profile_photo_path
        ]);

        // Use the provided role name (default to 'User' instead of 'user')
        $role = Role::where(['name' => $roleName])->first();
        
        // Debug log
        Log::info('Looking for role: ' . $roleName . ', found: ' . ($role ? 'yes (ID: ' . $role->id . ')' : 'no'));
        
        if (!$role) {
            // Fallback to 'User' role if specified role doesn't exist
            $role = Role::where(['name' => 'User'])->first();
            Log::info('Fallback to User role, found: ' . ($role ? 'yes (ID: ' . $role->id . ')' : 'no'));
        }

        $user->syncRoles([$role->id]);

        if ($verify) {
            event(new Registered($user));
        }

        $user->token = $user->createToken($user->email)->plainTextToken;

        if ($provider != 'credentials') {
            $user->provider()->updateOrCreate(
                ['provider' => $provider],
                [
                    'provider_id' => $socialUser->id,
                    'avatar' => $socialUser->avatar
                ]
            );
        }

        return $user;
    }
}
