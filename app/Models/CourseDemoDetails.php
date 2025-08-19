<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseDemoDetails extends Model
{
    use HasFactory;
    public $table = "course_demo_details";

    protected $fillable = [
        'id','user_id','email', 'mobile_number','dob','learn_language_id','learning_level_id','why_are_you_learing_this_language_id','country_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function learningLevel()
    {
        return $this->belongsTo(learningLevel::class);
    }

    public function language()
    {
        return $this->belongsTo(languag::class, 'learn_language_id');
    }

    public function country()
    {
        return $this->belongsTo(contry::class);
    }

}
