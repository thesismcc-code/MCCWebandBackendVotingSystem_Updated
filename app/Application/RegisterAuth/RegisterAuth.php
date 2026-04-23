<?php

namespace App\Application\RegisterAuth;

use App\Domain\User\User;
use App\Domain\Auth\AuthRepository;

class RegisterAuth
{
    private AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function login(string $email, string $password): ?User
    {
        if (empty($email) || empty($password)) {
            throw new \InvalidArgumentException('Email and password are required.');
        }

        $user = $this->authRepository->login($email, $password);

        if (!$user) {
            throw new \InvalidArgumentException('Invalid email or password.');
        }

        return $user;
    }
    public function logout(string $user_id): bool
    {
        return $this->authRepository->logout($user_id);
    }
    public function loginJwt(string $email, string $password): string
    {
        if (empty($email) || empty($password)) {
            throw new \InvalidArgumentException('Email and password are required.');
        }

        $token = $this->authRepository->loginJwt($email, $password);

        if (!$token) {
            throw new \InvalidArgumentException('Invalid email or password.');
        }

        return $token;
    }

    public function logoutJwt(string $token): bool
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('Token is required.');
        }

        return $this->authRepository->logoutJwt($token);
    }

    public function loginWithStudentID(string $studentId, string $password): User
    {
        return $this->authRepository->loginWithStudentID($studentId, $password);
    }

    public function loginJwtWithStudentID(string $studentId, string $password): string
    {
        return $this->authRepository->loginJwtWithStudentID($studentId, $password);
    }
}

