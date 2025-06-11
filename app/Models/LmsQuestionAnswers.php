<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsQuestionAnswers extends Model
{
    use HasFactory;
    public $table = "lms_question_answers";

    protected $fillable = [
        'id',
        'question_id',
        'user_id',
        'selected_answer',
        'is_correct'
    ];
}
