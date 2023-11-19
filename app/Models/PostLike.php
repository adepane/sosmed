<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostLike extends Model
{
    protected $table = 'post_likes';

    protected $fillable = [
        'id',
        'post_id',
        'user_id',
    ];

    /**
     * Get the user that owns the Post Like
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
