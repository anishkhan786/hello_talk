<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McqCompleteTopicPage extends Model
{
    use HasFactory;
    public $table = "mcq_complete_topic_page";

    protected $fillable = [
        'id','topic_id','user_id', 'page_number',
    ];
}
