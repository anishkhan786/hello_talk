<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSettings extends Model
{
    use HasFactory;
    public $table = "group_settings";

    protected $fillable = [
        'id',
        'group_id',
        'user_id',
        'notifications',
        'mute',
        'blocked',
        'block_date',
        'last_cleared_at',
    ];

   
}
