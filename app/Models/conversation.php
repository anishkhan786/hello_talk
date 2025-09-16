<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class conversation extends Model
{
    protected $fillable = 
    [
        'user_one_id', 
        'user_two_id',
        'user_one_chat_delete',
        'user_two_chat_delete',
        'user_one_block',
        'user_two_block',
        'one_user_notification',
        'two_user_notification',
        'one_user_call',
        'two_user_call',
    ];

    public function messages()
    {
        return $this->hasMany(message::class);
    }

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }
}
