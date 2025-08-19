<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class learningLevel extends Model
{
    use HasFactory;
    public $table = "learning_level";

    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];
}
