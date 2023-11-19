<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
        'id',
        'user_id',
        'caption',
    ];

    /**
     * Get all of the images for the Post
     */
    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class, 'post_id', 'id');
    }

    /**
     * Get all of the likes for the Post
     */
    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class, 'post_id', 'id');
    }

    /**
     * Get all of the comments for the Post
     */
    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class, 'post_id', 'id')->where('parent_id', 0);
    }
}
