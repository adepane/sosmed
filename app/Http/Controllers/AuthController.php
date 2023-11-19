<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private AuthRepositoryInterface $authRepository;
    public $helperClass;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
        $this->helperClass = new Helper;
    }

    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'username'  => 'required',
            'password'  => 'required'
        ]);

        
        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }
        
        $userOrEmail = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return $this->authRepository->login($request->all(), $userOrEmail);
   }

   public function register(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'first_name'      => 'required',
            'last_name'      => 'required',
            'username'     => 'required|unique:users',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        return $this->authRepository->register($request->all());
        
   }
}
