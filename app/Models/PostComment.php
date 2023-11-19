<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostComment extends Model
{
    protected $table = 'post_comments';

    protected $fillable = [
        'id',
        'post_id',
        'parent_id',
        'user_id',
        'comment',
    ];

    /**
     * Get the user that owns the Post Like
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get all of the childComments for the PostComment
     */
    public function childComments(): HasMany
    {
        return $this->hasMany($this::class, 'parent_id', 'id');
    }

    /**
     * Get the post that owns the PostComment
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
