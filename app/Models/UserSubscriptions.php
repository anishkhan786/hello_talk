<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscriptions extends Model
{
    use HasFactory;
    public $table = "user_subscriptions";

    protected $fillable = [
        'id','user_id','plan_id', 'start_date','end_date','amount','payment_status','payment_method','transaction_id','status'
    ];
}
