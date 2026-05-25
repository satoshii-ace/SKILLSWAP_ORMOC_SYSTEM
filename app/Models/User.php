<?php

namespace App\Models;


use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Transaction;

class User extends Authenticatable
{
    
    use HasFactory, Notifiable;

    
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'google_access_token',
        'google_refresh_token',
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
        'google_access_token',
        'google_refresh_token',
    ];
    


    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'google_access_token' => 'array',
        ];
    }
    
    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    
    public function acceptedSwaps()
    {
        return Transaction::where('status', 'accepted')
            ->where(function ($query) {
                $query->where('provider_id', $this->id)
                      ->orWhere('receiver_id', $this->id);
            });
    }

    
    public function getSwapStreakAttribute()
    {
        return $this->acceptedSwaps()->count();
    }
}
