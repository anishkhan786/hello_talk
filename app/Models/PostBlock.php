<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostBlock extends Model
{
    use HasFactory;
    public $table = "post_blocks";
    protected $fillable = ['id', 'user_id','post_id','text'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}