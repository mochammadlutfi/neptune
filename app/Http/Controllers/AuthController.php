<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Browser;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | API Authentication Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating admin users for the application and
    | redirecting them to your admin dashboard.
    |
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Login User.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // dd($request->all());
        try {
            // Enhanced input validation with security rules
            $validated = $request->validate([
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:8|max:255'
            ]);
            // // Rate limiting key based on IP and email
            // $rateLimitKey = 'login:' . $request->ip() . ':' . $validated['email'];
            
            // // Check rate limiting (5 attempts per minute)
            // if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            //     $seconds = RateLimiter::availableIn($rateLimitKey);
                
            //     Log::warning('Login rate limit exceeded', [
            //         'ip' => $request->ip(),
            //         'email' => $validated['email'],
            //         'user_agent' => $request->userAgent()
            //     ]);
                
            //     return response()->json([
            //         'success' => false,
            //         'message' => "Too many login attempts. Please try again in {$seconds} seconds."
            //     ], 429);
            // }

            // Find user by email
            $user = User::where('email', $validated['email'])->first();
            // dd($user);
            // Check if user exists and password is correct
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                // Increment rate limiting counter on failed attempt
                // RateLimiter::hit($rateLimitKey, 60); // 60 seconds decay
                
                // Log failed login attempt
                Log::warning('Failed login attempt', [
                    'ip' => $request->ip(),
                    'email' => $validated['email'],
                    'user_agent' => $request->userAgent()
                ]);
                
                // Generic error message to prevent user enumeration
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
            // Clear rate limiting on successful login
            // RateLimiter::clear($rateLimitKey);

            // Generate device information
            $device = Browser::platformName() . ' / ' . Browser::browserName();
    
            // Create token with proper expiration
            $tokenExpiry = $request->boolean('remember') ? now()->addMonth() : now()->addDay();
            $sanctumToken = $user->createToken($device, ['*'], $tokenExpiry);
    
            // Store IP address for security tracking
            $sanctumToken->accessToken->ip = $request->ip();
            $sanctumToken->accessToken->save();

            // Prepare response data
            $responseData = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'image' => $user->image,
                    'image_url' => $user->image_url,
                    'roles' => $user->roles,
                    'vessel_id' => $user->vessel_id,
                    'vessels' => $user->vessels
                ],
                'access_token' => $sanctumToken->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $tokenExpiry->toISOString(),
                'permissions' => $user->getPermissionsViaRoles()->pluck('name'),
            ];

            // Log successful login
            Log::info('Successful login', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'device' => $device
            ]);

            return response()->json([
                'success' => true,
                'result' => $responseData,
                'message' => 'Login successful'
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Logout the admin.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            
            if ($user) {
                // Log logout activity
                Log::info('User logout', [
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);
                
                // Delete all tokens for this user (or just current token)
                $user->tokens()->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Logged out successfully'
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
            
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
