<?php

namespace App\Repository;

use App\Helper\Helper;
use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AuthRepository implements AuthRepositoryInterface
{
    public $userModel;

    public $helperClass;

    public function __construct()
    {
        $this->userModel = new User;
        $this->helperClass = new Helper;
    }

    public function register($parameters)
    {
        try {
            DB::beginTransaction();

            $user = $this->userModel->create([
                'first_name' => $parameters['first_name'],
                'last_name' => $parameters['last_name'],
                'email' => $parameters['email'],
                'username' => $parameters['username'],
                'password' => bcrypt($parameters['password']),
            ]);

            DB::commit();

            return $this->helperClass->apiResponse(true, $user, 'Your acccount created successfully');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->helperClass->apiResponse(false, [], $e->getMessage());
        }
    }

    public function login(array $parameters, string $usernameOrEmail)
    {

        $email = $parameters['username'];

        if ($usernameOrEmail === 'username') {
            $user = $this->userModel->findEmailByUsername($parameters['username']);
            $email = $user->email;
        }

        $oauth2 = Http::asForm()->post(route('passport.token'), [
            'grant_type' => 'password',
            'client_id' => config('services.passport.id'),
            'client_secret' => config('services.passport.secret'),
            'username' => $email,
            'password' => $parameters['password'],
            'scope' => '*',
        ]);

        $response = collect($oauth2->json());

        if ($response->has('error')) {
            return $this->helperClass->apiResponse(false, $response, $response['message']);
        }

        $reconstructResponse = [
            'tokenType' => $response['token_type'],
            'expiresIn' => $response['expires_in'],
            'accessToken' => $response['access_token'],
            'refreshToken' => $response['refresh_token'],
        ];

        return $this->helperClass->apiResponse(true, $reconstructResponse, 'Login success');
    }
}
