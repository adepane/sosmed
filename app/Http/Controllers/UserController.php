<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public $helperClass;

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->helperClass = new Helper;
        $this->userRepository = $userRepository;
    }

    public function profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        return $this->userRepository->profile($request->userId);
    }

    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'numeric',
            'image' => 'image',
            'dob' => 'date_format:d/m/Y',
            'username' => 'unique:users,username,'.Auth::id(),
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        return $this->userRepository->profileUpdate(Auth::id(), $request->all());
    }

    public function getFollower(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        return $this->userRepository->getFollower($request->all());
    }

    public function getFollowing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        return $this->userRepository->getFollowing($request->all());
    }

    public function userSearch(Request $request)
    {
        return $this->userRepository->userSearch($request->all());
    }

    public function userFollow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'action' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        return $this->userRepository->userFollow($request->all());
    }
}
