<?php

namespace App\Interfaces;

interface PostRepositoryInterface
{
    public function getStory(array $parameters);

    public function addStory(array $parameters);

    public function deleteStory(array $parameters);

    public function likeStory(array $parameters);

    public function addStoryComment(array $parameters);

    public function deleteStoryComment(array $parameters);
}
