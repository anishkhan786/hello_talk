<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class agora_call extends Model
{
    protected $fillable = [
        'caller_id',
        'receiver_id',
        'channel_name',
        'agora_uid',
        'token',
        'started_at',
        'ended_at',
    ];
}
