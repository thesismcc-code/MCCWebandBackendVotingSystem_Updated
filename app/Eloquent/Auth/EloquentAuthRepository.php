<?php

namespace App\Eloquent\Auth;

use App\Domain\User\User;
use App\Domain\Auth\AuthRepository;
use App\Domain\User\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class EloquentAuthRepository implements AuthRepository
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->getPassword())) {
            // Log failed login attempt
            app(\App\Services\SecurityLogger::class)->logFailedLogin($email, 'Invalid credentials', request());
            return null;
        }

        Session::put('auth_user', [
            'id'                => $user->getId(),
            'first_name'        => $user->getFirstName(),
            'middle_name'       => $user->getMiddleName(),
            'last_name'         => $user->getLastName(),
            'email'             => $user->getEmail(),
            'role'              => $user->getRole(),
            'student_id'        => $user->getStudentId(),
            'comelec_id'        => $user->getComelecId(),
            'admin_id'          => $user->getAdminId(),
            'year_level'        => $user->getYearLevel(),
            'email_verified_at' => $user->getEmailVerifiedAt(),
        ]);

        Session::regenerate();

        return $user;
    }

    public function loginWithStudentID(string $studentId, string $password): User
    {
        if (!$this->userRepository->validateStudentID($studentId)) {
            throw new \InvalidArgumentException('Student ID not found.');
        }

        $user = $this->userRepository->findByStudentID($studentId);

        if (!$user || !Hash::check($password, $user->getPassword())) {
            throw new \InvalidArgumentException('Invalid Student ID or password.');
        }

        Session::put('auth_user', [
            'id'                 => $user->getId(),
            'email'              => $user->getEmail(),
            'role'               => $user->getRole(),
            'first_name'         => $user->getFirstName(),
            'last_name'          => $user->getLastName(),
            'student_id'         => $user->getStudentId(),
            'year_level'         => $user->getYearLevel(),
            'email_verified_at'  => $user->getEmailVerifiedAt(),
        ]);

        return $user;
    }

    public function logout(string $user_id): bool
    {
        try {
            Session::forget('auth_user');
            Session::invalidate();
            Session::regenerateToken();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function loginJwt(string $email, string $password): ?string
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->getPassword())) {
            return null;
        }

        try {
            $payload = JWTAuth::factory()->customClaims([
                'sub'         => $user->getId(),
                'email'       => $user->getEmail(),
                'role'        => $user->getRole(),
                'first_name'  => $user->getFirstName(),
                'last_name'   => $user->getLastName(),
                'student_id'  => $user->getStudentId(),
                'comelec_id'  => $user->getComelecId(),
                'admin_id'    => $user->getAdminId(),
            ])->make();

            return JWTAuth::encode($payload)->get();
        } catch (JWTException $e) {
            return null;
        }
    }

    
    public function loginJwtWithStudentID(string $studentId, string $password): string
    {
        if (!$this->userRepository->validateStudentID($studentId)) {
            throw new \InvalidArgumentException('Student ID not found.');
        }
        $user = $this->userRepository->findByStudentID($studentId);
        if (!password_verify($password, $user->getPassword())) {
            throw new \InvalidArgumentException('Invalid Student ID or password.');
        }
        try {
            $payload = JWTAuth::factory()->customClaims([
                'sub'        => $user->getId(),
                'student_id' => $user->getStudentId(),
                'first_name' => $user->getFirstName(),
                'last_name'  => $user->getLastName(),
                'role'       => $user->getRole(),
            ])->make();
            return JWTAuth::encode($payload)->get();
        } catch (JWTException $e) {
            throw new \RuntimeException('Could not create token.');
        }
    }
    public function logoutJwt(string $token): bool
    {
        try {
            JWTAuth::setToken($token)->invalidate();
            return true;
        } catch (JWTException $e) {
            return false;
        }
    }
}

