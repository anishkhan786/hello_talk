<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;
    public $table = "post_comments";
    protected $fillable = ['id','user_id', 'post_id','comment'];

     public function user()
    {
        return $this->belongsTo(User::class);
    }
}