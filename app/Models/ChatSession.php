<?php

namespace App\Models;  // Or App\ in older Laravel versions

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = [
        'session_id',
        'context',
        'conversation_history'
    ];

    protected $casts = [
        'context' => 'array',
        'conversation_history' => 'array'
    ];
}