<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingUserEventLogs extends Model
{
    use HasFactory;
    public $table = "marketing_user_event_logs";

     protected $fillable = [
        'id','user_id', 'event_type','related_user_id', 'view_date','data',
    ];

}
