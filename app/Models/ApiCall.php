<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiCall extends Model
{
    protected $fillable = [
        'user_id',
        'endpoint',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 