<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingView extends Model
{
    use HasFactory;
    public $table = "marketing_click_views";

     protected $fillable = [
        'id','user_id', 'marketing_item_id', 'view_date',
    ];

}
