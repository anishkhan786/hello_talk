<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostReports extends Model
{
    use HasFactory;
    public $table = "post_reports";
    protected $fillable = ['id', 'user_id','post_id','reason'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}