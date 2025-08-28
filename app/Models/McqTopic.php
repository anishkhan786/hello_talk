<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McqTopic extends Model
{
    use HasFactory;
    public $table = "mcq_topics";

    protected $fillable = [
        'id','name', 'description','learning_level','language_id'
    ];

    public function language()
    {
        return $this->belongsTo(languag::class ,'language_id');
    }

    public function learninglevel()
    {
        return $this->belongsTo(learningLevel::class ,'learning_level');
    }
}
