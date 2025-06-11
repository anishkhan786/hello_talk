<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TroopersTogether extends Model
{
    use HasFactory;
    public $table = "troopers_togethers";

    protected $fillable = [
        'id',
        'primary_counsellor',
        'status',
        'session_title',
        'session_description',
        'start_time',
        'date',
        'duration',
        'time_left',
        'session_type',
        'close_by',
    ];
}
