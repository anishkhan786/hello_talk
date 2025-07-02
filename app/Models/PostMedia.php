<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    use HasFactory;
    public $table = "post_media";
    protected $fillable = ['post_id', 'media_path'];

    public function post()
    {
        return $this->belongsTo(Posts::class ,'post_id');
    }
}
