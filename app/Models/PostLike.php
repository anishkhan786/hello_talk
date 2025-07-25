<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasFactory;
    public $table = "post_likes";
    protected $fillable = ['id', 'user_id','post_id'];

     public function user()
    {
        return $this->belongsTo(User::class);
    }
}