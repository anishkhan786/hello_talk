<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McqOption extends Model
{
    use HasFactory;
    public $table = "mcq_question_options";

    protected $fillable = [
        'id','question_id','option_text', 'match_key','is_correct',
    ];

    public function question()
    {
        return $this->belongsTo(McqQuestion::class);
    }
}
