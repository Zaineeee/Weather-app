<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $table = 'login_history';

    protected $fillable = [
        'user_id',
        'ip_address',
        'logout_time'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'logout_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 