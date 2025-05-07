<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Message;
use App\Models\User;

class Conversation extends Model
{
    protected $casts = [
        'user_ids' => 'array'
    ];
    protected $fillable = [
        'name',
        'user_ids'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}


// Conversation holds messages and users
// A user can have many conversations
// A conversation can have many messages
// A message belongs to a conversation
// A message belongs to a user
// A user can have many messages
