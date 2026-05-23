<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'google_access_token',
        'google_refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_access_token',
        'google_refresh_token',
    ];
    // In App\Models\User.php


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'google_access_token' => 'array',
        ];
    }
    /**
     * Get the skills associated with the user.
     */
    public function skills()
    {
        return $this->hasMany(Skill::class);
    }
    public function getSwapStreakAttribute()
{
    // Instead of calling $this->swaps() which doesn't exist,
    // we use the 'skills' relationship we know is working.
    // This counts skills posted in the last 3 weeks as a "streak" proxy.
    return $this->skills()
        ->where('created_at', '>=', now()->subWeeks(3))
        ->count();
}
}
