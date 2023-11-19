<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Interfaces\PostRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public $helperClass;
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->helperClass = new Helper;
        $this->postRepository = $postRepository;
    }

    public function getStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'postId'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        return $this->postRepository->getStory($request->all());
    }

    public function addStory(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'image'  => 'array',
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        foreach ($request->image as $image) {
            $imageStory = ['image'=> $image];
            
            $imageValidator = Validator::make($imageStory, [
                'image'  => 'required|image',
            ]);

            if ($imageValidator->fails()) {
                return $this->helperClass->apiResponse(false, [], $imageValidator->errors());
            }
        }

        return $this->postRepository->addStory($request->all());
        
    }

    public function deleteStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'postId'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        return $this->postRepository->deleteStory($request->all());
    }

    public function likeStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'postId'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        return $this->postRepository->likeStory($request->all());
    }

    public function addStoryComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'postId'  => 'required',
            'comment' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        return $this->postRepository->addStoryComment($request->all());
    }

    public function deleteStoryComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commentId'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->helperClass->apiResponse(false, [], $validator->errors());
        }

        return $this->postRepository->deleteStoryComment($request->all());
    }

}
