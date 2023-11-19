<?php

namespace App\Repository;

use App\Models\Post;
use App\Models\User;
use App\Helper\Helper;
use App\Models\PostLike;
use App\Models\PostImage;
use App\Models\PostComment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{

    public $userModel;
    public $postModel;
    public $postImageModel;
    public $postLikeModel;
    public $postCommentModel;
    public $helperClass;

    public function __construct() {
        $this->userModel = new User;
        $this->postModel = new Post;
        $this->postImageModel = new PostImage;
        $this->postLikeModel = new PostLike;
        $this->postCommentModel = new PostComment;
        $this->helperClass = new Helper;
    }

    public function getStory(array $parameters)
    {
        try {
            $post = $this->postModel
                        ->select([
                            'id',
                            'user_id as userId',
                            'caption'
                        ])
                        ->with('likes.user:id,first_name as firstName,last_name as lastName,username')
                        ->with('comments.childComments')
                        ->withCount('likes as likesCount')
                        ->findOrFail($parameters['postId']);

            return $this->helperClass->apiResponse(true, $post, 'Story upload successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->helperClass->apiResponse(false, [], $e->getMessage());
        }
    }
    
    public function addStory(array $parameters)
    {
        try {
            DB::beginTransaction();

            $post = $this->postModel->create([
                'user_id' => Auth::id(),
                'caption' => $parameters['caption'] ?? NULL
            ]);

            foreach ($parameters['image'] as $image) {
                $imageName = Str::uuid() . '-' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(base_path('public/posts'), $imageName);
                $imagePath = '/posts/' . $imageName;

                $this->postImageModel->create([
                    'post_id' => $post->id,
                    'path' => $imagePath,
                ]);
            }

            DB::commit();

            $result = [
                'post' => $this->postModel->with('images')->find($post->id),
            ];

            return $this->helperClass->apiResponse(true, $result, 'Story upload successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->helperClass->apiResponse(false, [], $e->getMessage());
        }
    }

    public function deleteStory(array $parameters)
    {
        try {
            DB::beginTransaction();

            $post = $this->postModel->with('images')->findOrFail($parameters['postId']);

            $post->images()->delete();
            $post->delete();

            DB::commit();

            return $this->helperClass->apiResponse(true, [], 'Story has been deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->helperClass->apiResponse(false, [], $e->getMessage());
        }
    }

    public function likeStory(array $parameters)
    {
        try {
            DB::beginTransaction();

            $postQuery = $this->postLikeModel
                        ->where('post_id', $parameters['postId'])
                        ->where('user_id', Auth::id());

            $message = '';
            if ($postQuery->exists()) {
                $postQuery->first()->delete();
                $message = 'Unlike the story';
            } else {
                $this->postLikeModel->create([
                    'post_id' => $parameters['postId'],
                    'user_id' => Auth::id()
                ]);
                $message = 'Like the story';
            }

            DB::commit();

            return $this->helperClass->apiResponse(true, [], $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->helperClass->apiResponse(false, [], $e->getMessage());
        }
    }

    public function addStoryComment(array $parameters)
    {
        try {
            DB::beginTransaction();

            $parentComment = $parameters['parentId'] ?? 0;

            if ($parentComment !== 0) {
                $checkParentComment = $this->postCommentModel->findOrFail($parentComment);
                if ($checkParentComment->parent_id !== 0) {
                    return $this->helperClass->apiResponse(false, [], 'Max of indent of comment is 2 level');
                }
            }

            $comment = $this->postCommentModel->create([
                'post_id' => $parameters['postId'],
                'comment' => $parameters['comment'],
                'parent_id' => $parentComment,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            return $this->helperClass->apiResponse(true, $comment, 'Commented');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->helperClass->apiResponse(false, [], $e->getMessage());
        }
    }

    public function deleteStoryComment(array $parameters)
    {
        try {
            DB::beginTransaction();
            
            $comment = $this->postCommentModel->with('post')->findOrFail($parameters['commentId']);

            $userCanModify = [$comment->user_id, $comment->post->user_id];
            
            if (!in_array(Auth::id(), $userCanModify)) {
                return $this->helperClass->apiResponse(false, [], 'You dont have access to modified this');
            }

            $comment->delete();

            DB::commit();

            return $this->helperClass->apiResponse(true, [], 'Comment has been deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->helperClass->apiResponse(false, [], $e->getMessage());
        }
    }
    
}
