<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;
    public $table = "posts";
    protected $fillable = ['user_id', 'post_type', 'content', 'media_path','caption','location'];

    public function media()
    {
        return $this->hasMany(PostMedia::class,'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes() {
    return $this->hasMany(PostLike::class,'post_id');
    }

    public function comments() {
        return $this->hasMany(PostComment::class ,'post_id');
    }

    public function shares() {
        return $this->hasMany(PostShare::class,'post_id');
    }

    public function isLikedBy($userId){
        return $this->likes()->where('user_id', $userId)->exists();
    }

}
