<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'unique_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function loginHistory()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function lastLogin()
    {
        return $this->hasOne(LoginHistory::class)->latest();
    }
}
