<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFriendship extends Model
{
    protected $table = 'user_friendships';

    protected $fillable = [
        'id',
        'user_id',
        'follower_id',
    ];

    /**
     * Get the user that owns the Post Like
     */
    public function followingDetail(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the user that owns the Post Like
     */
    public function followerDetail(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id', 'id');
    }
}
