<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupsMessages extends Model
{
    use HasFactory;
    public $table = "groups_messages";

    protected $fillable = [
        'id',
        'group_id',
        'user_id',
        'message_type',
        'content',
    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }
}
