<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;
    public $table = "subscription_plans";

    protected $fillable = [
        'id','name','duration_type', 'duration_value','price','discounted_price','status',
    ];
}
