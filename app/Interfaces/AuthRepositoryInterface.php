<?php

namespace App\Interfaces;

interface AuthRepositoryInterface
{
    public function register(array $parameters);

    public function login(array $parameters, string $usernameOrEmail);
}
