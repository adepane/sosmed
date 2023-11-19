<?php

namespace App\Repository;

use App\Helper\Helper;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\UserFriendship;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public $userModel;

    public $userFriendshipModel;

    public $helperClass;

    public function __construct()
    {
        $this->userModel = new User;
        $this->userFriendshipModel = new UserFriendship;
        $this->helperClass = new Helper;
    }

    public function profile($userId)
    {
        try {
            $user = $this->userModel
                ->select([
                    'id',
                    'first_name as firstName',
                    'last_name as lastName',
                    'username',
                    'image',
                    'dob',
                    'phone',
                ])
                ->withCount(['followings as followingsTotal', 'followers as followersTotal'])
                ->findOrFail($userId);

            $isYouFollow = false;
            $isFollowYou = false;
            $isYourSelf = ($userId == Auth::id()) ? true : false;

            if ($userId != Auth::id()) {
                $isYouFollow = $this->userFriendshipModel->where('user_id', $userId)->where('follower_id', Auth::id())->exists();
                $isFollowYou = $this->userFriendshipModel->where('user_id', Auth::id())->where('follower_id', $userId)->exists();
            }

            $user->isYouFollow = $isYouFollow;
            $user->isFollowYou = $isFollowYou;
            $user->isYourSelf = $isYourSelf;

            return $this->helperClass->apiResponse(true, $user, 'User fetch data');

        } catch (\Exception $e) {
            return $this->helperClass->apiResponse(false, [], $e->getMessage());
        }

    }

    public function profileUpdate(string $userId, array $parameters)
    {
        try {

            $user = $this->userModel->findOrFail($userId);
            $imagePath = null;
            if (collect($parameters)->has('image')) {
                $imageName = Str::uuid().'-'.time().'.'.$parameters['image']->getClientOriginalExtension();
                $parameters['image']->move(base_path('public/images'), $imageName);
                $imagePath = '/images/'.$imageName;
            }

            DB::beginTransaction();
            $user->update([
                'first_name' => $parameters['first_name'] ?? $user->first_name,
                'last_name' => $parameters['last_name'] ?? $user->last_name,
                'dob' => ! empty($parameters['dob']) ? Carbon::createFromFormat('d/m/Y', $parameters['dob']) : $user->dob,
                'username' => $parameters['username'] ?? $user->username,
                'phone' => $parameters['phone'] ?? $user->phone,
                'image' => $imagePath ?? $user->image,
            ]);
            DB::commit();

            $createArrayResponse = [
                'user' => $user,
            ];

            return $this->helperClass->apiResponse(true, $createArrayResponse, 'User fetch data');

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->helperClass->apiResponse(false, [], $e->getMessage());
        }
    }

    public function getFollower(array $parameters)
    {
        $followers = $this->userFriendshipModel
            ->select(['id', 'user_id', 'follower_id'])
            ->with('followerDetail:id,first_name as firstName,last_name as lastName,username')
            ->where('user_id', $parameters['userId'])->get();

        return $this->helperClass->apiResponse(true, $followers, 'Followers fetch data');
    }

    public function getFollowing(array $parameters)
    {
        $followings = $this->userFriendshipModel
            ->select(['id', 'user_id', 'follower_id'])
            ->with('followingDetail:id,first_name as firstName,last_name as lastName,username')
            ->where('follower_id', $parameters['userId'])->get();

        return $this->helperClass->apiResponse(true, $followings, 'Followings fetch data');
    }

    public function userSearch(array $query)
    {
        $skip = $query['skip'] ?? 0;
        $take = $query['take'] ?? 10;
        $username = $query['username'] ?? null;
        $search = $query['query'] ?? null;

        $searchQuery = $this->userModel->where(function ($q) use ($username, $search) {
            if (! empty($username)) {
                return $q->where('username', $username);
            }

            return $q->where('username', 'like', $search)
                ->orWhere('first_name', 'like', '%'.$search.'%')
                ->orWhere('last_name', 'like', '%'.$search.'%')
                ->orWhere('last_name', 'like', '%'.$search.'%');
        })
            ->when($skip !== 0, function ($q) use ($skip) {
                $q->skip($skip);
            })
            ->take($take)
            ->orderBy('id', 'desc')
            ->get();

        $searchResult = [
            'searchResult' => $searchQuery,
            'length' => $searchQuery->count(),
        ];

        return $this->helperClass->apiResponse(true, $searchResult, 'Search result');
    }

    public function userFollow(array $parameters)
    {
        $action = $parameters['action'];
        $followingId = $parameters['userId'];
        switch ($action) {
            case 'follow':

                if (Auth::id() == $followingId) {
                    return $this->helperClass->apiResponse(false, [], 'Can\'t follow himself ');
                }

                $isAlreadyFollow = $this->userFriendshipModel->where('user_id', $followingId)
                    ->where('follower_id', Auth::id());

                if ($isAlreadyFollow->exists()) {
                    return $this->helperClass->apiResponse(false, [], 'Already following');
                }

                $this->userFriendshipModel->create([
                    'user_id' => $followingId,
                    'follower_id' => Auth::id(),
                ]);

                return $this->helperClass->apiResponse(true, [], 'Success following');
                break;

            case 'unfollow':

                if (Auth::id() == $followingId) {
                    return $this->helperClass->apiResponse(false, [], 'Can\'t unfollow himself ');
                }

                $userFollow = $this->userFriendshipModel->where('user_id', $followingId)
                    ->where('follower_id', Auth::id());

                if ($userFollow->exists()) {
                    $userFollow->delete();

                    return $this->helperClass->apiResponse(true, [], 'Success unfollow');
                }

                return $this->helperClass->apiResponse(false, [], 'Not following');
                break;
        }

    }
}
