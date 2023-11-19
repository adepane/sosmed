<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\UserFriendship;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'dob',
        'phone',
        'image',
        'dob'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function findEmailByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Get all of the followers from the User
     */
    public function followers(): HasMany
    {
        return $this->hasMany(UserFriendship::class, 'user_id', 'id');
    }

    /**
     * Get all of the following from the User
     */
    public function followings(): HasMany
    {
        return $this->hasMany(UserFriendship::class, 'follower_id', 'id');
    }
}
