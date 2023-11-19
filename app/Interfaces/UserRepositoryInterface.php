<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{

    public function profile(integer $userId);
    public function profileUpdate(string $userId, array $parameters);
    public function getFollower(array $parameters);
    public function getFollowing(array $parameters);
    public function userSearch(array $query);
    public function userFollow(array $parameters);
    
}