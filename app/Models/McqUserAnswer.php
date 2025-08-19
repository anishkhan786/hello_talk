<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McqUserAnswer extends Model
{
    use HasFactory;
    public $table = "mcq_answers";

    protected $fillable = [
        'id','question_id','user_id', 'answer_text','option_id','is_correct',
    ];

    public function question()
    {
        return $this->belongsTo(McaQuestion::class);
    }
}
