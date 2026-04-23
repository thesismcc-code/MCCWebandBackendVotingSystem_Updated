<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Application\RegisterAuth\RegisterAuth;
use App\Services\SecurityLogger;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    private RegisterAuth $registerAuth;
    private SecurityLogger $logger;

    public function __construct(RegisterAuth $registerAuth, SecurityLogger $logger)
    {
        $this->registerAuth = $registerAuth;
        $this->logger = $logger;
    }

    public function index()
    {
        return view('index');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ], $this->authValidationMessages());

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        try {
            $user = $this->registerAuth->login(
                $request->input('email'),
                $request->input('password')
            );

            // Log successful login
            $this->logger->log('info', ucfirst($user->getRole()) . ' logged in', ucfirst($user->getRole()));

            return $this->redirectByRole($user->getRole());
        } catch (\InvalidArgumentException $e) {
            // Log failed login
            $this->logger->log('error', 'Failed login attempt: ' . $e->getMessage(), 'Unknown');
            
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function studentIndex()
    {
        if (Session::has('auth_user')) {
            return $this->redirectByRole(Session::get('auth_user.role'));
        }

        return view('student.loginpage');
    }

    public function studentLogin(Request $request)
    {
        $isStudentID = $request->filled('student_id');

        if ($isStudentID) {
            $validator = Validator::make($request->all(), [
                'student_id' => 'required|string',
                'password'   => 'required',
            ], [
                'student_id.required' => 'Student ID is required.',
                'password.required'   => 'Password is required.',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required',
            ], $this->authValidationMessages());
        }

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        try {
            if ($isStudentID) {
                $user = $this->registerAuth->loginWithStudentID(
                    $request->input('student_id'),
                    $request->input('password')
                );
            } else {
                $user = $this->registerAuth->login(
                    $request->input('email'),
                    $request->input('password')
                );

                if ($user->getRole() !== 'student') {
                    return back()
                        ->withInput()
                        ->with('error', 'Access denied. This login is for students only.');
                }
            }

            // Log successful student login
            $this->logger->log('info', 'Student logged in', 'Student');

            // All students must verify their email via OTP before accessing the dashboard
            // Once verified (email_verified_at is set), they go straight to dashboard on future logins
            $isVerified = !empty($user->getEmailVerifiedAt());

            if ($isVerified) {
                return redirect()->route('view.student-dashboard');
            }

            // Redirect to OTP verification
            return redirect()->route('view.student-verification');
        } catch (\InvalidArgumentException $e) {
            // Log failed student login
            $this->logger->log('error', 'Failed student login: ' . $e->getMessage(), 'Student');
            
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }
    public function logout()
    {
        $role = Session::get('auth_user.role', 'admin');
        $userId = Session::get('auth_user.id', '');
        $this->logger->log('info', ucfirst($role) . ' logged out', ucfirst($role));
        $this->registerAuth->logout($userId);

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
    public function logoutStudent()
    {
        $userId = Session::get('auth_user.id', '');
        $this->logger->log('info', 'Student logged out', 'Student');
        $this->registerAuth->logout($userId);

        return redirect('/students')
            ->with('success', 'You have been logged out successfully.');
    }
    public function loginAPI(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ], $this->authValidationMessages());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $token = $this->registerAuth->loginJwt(
                $request->input('email'),
                $request->input('password')
            );

            return response()->json([
                'success'      => true,
                'message'      => 'Login successful.',
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => config('jwt.ttl') * 60,
            ], 200);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }
    public function logoutAPI(Request $request): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not provided.',
                ], 401);
            }

            $this->registerAuth->logoutJwt((string) $token);

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
            ], 200);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token has already expired.',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token is invalid.',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout. Please try again.',
            ], 500);
        }
    }
    public function meAPI(Request $request): JsonResponse
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'          => $payload->get('sub'),
                    'email'       => $payload->get('email'),
                    'role'        => $payload->get('role'),
                    'first_name'  => $payload->get('first_name'),
                    'last_name'   => $payload->get('last_name'),
                    'student_id'  => $payload->get('student_id'),
                    'teacher_id'  => $payload->get('teacher_id'),
                    'admin_id'    => $payload->get('admin_id'),
                ],
            ], 200);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token has expired.',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token is invalid.',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided.',
            ], 401);
        }
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'   => redirect()->route('view.dashboard'),
            'sao'     => redirect()->route('view.sao-dashboard'),
            'comelec' => redirect()->route('view.comelec-dashboard'),
            'student' => redirect()->route('view.student-dashboard'),
            default   => redirect()->route('login'),
        };
    }

    private function authValidationMessages(): array
    {
        return [
            'email.required'    => 'Email is required.',
            'email.email'       => 'That email address doesn\'t look valid.',
            'password.required' => 'Password is required.',
        ];
    }
}
