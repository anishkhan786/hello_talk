<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingUserView extends Model
{
    use HasFactory;
    public $table = "marketing_user_view";

     protected $fillable = [
        'id','user_id', 'marketing_item_id','view_round', 'view_date',
    ];

}
