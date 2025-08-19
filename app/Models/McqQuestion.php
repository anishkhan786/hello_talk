<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McqQuestion extends Model
{
    use HasFactory;
    public $table = "mcq_questions";

     protected $fillable = [
        'id','topic_id','title', 'description','type','marks',
    ];

    public function topic()
    {
        return $this->belongsTo(McqTopic::class ,'topic_id');
    }

    public function mcqOptions()
    {
        return $this->hasMany(McqOption::class ,'question_id')->select('id', 'question_id','option_text','match_key','is_correct');
    }

    // public function userAnswers()
    // {
    //     return $this->hasMany(UserAnswer::class ,'post_id');
    // }
}
