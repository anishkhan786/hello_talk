<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsQuestions extends Model
{
    use HasFactory;
    public $table = "lms_questions";

    protected $fillable = [
        'id',
        'course_id',
        'category_id',
        'question_text',
        'explanation',
        'question_type',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'marks',
        'is_active',
    ];
}
