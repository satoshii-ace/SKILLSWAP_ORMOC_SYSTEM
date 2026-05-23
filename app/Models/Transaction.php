<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_id', 
        'provider_id', 
        'receiver_id', 
        'status', 
        'scheduled_date'
    ];

    // ADD THIS BLOCK: It transforms the string into a Carbon Date Object
    protected $casts = [
        'scheduled_date' => 'datetime',
    ];

    public function skill() {
        return $this->belongsTo(Skill::class);
    }

    public function provider() {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}