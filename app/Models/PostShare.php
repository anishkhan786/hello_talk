<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostShare extends Model
{
    use HasFactory;
    public $table = "post_shares";
    protected $fillable = ['id', 'user_id','post_id'];
}