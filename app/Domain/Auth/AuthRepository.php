<?php

namespace App\Domain\Auth;

use App\Domain\User\User;

interface AuthRepository
{
    public function login(string $email, string $password): ?User;
    public function logout(string $user_id): bool;
    public function loginJwt(string $email, string $password): ?string;
    public function logoutJwt(string $token): bool;
    public function loginWithStudentID(string $studentId, string $password): User;
    public function loginJwtWithStudentID(string $studentId, string $password): string;
}

