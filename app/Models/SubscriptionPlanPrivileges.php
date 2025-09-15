<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlanPrivileges extends Model
{
    use HasFactory;
    public $table = "subscription_plan_privileges";

    protected $fillable = [
        'id','plan_id','privilege_id', 'access_type','limit_value',
    ];

     // Relations
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function privilege()
    {
        return $this->belongsTo(SubscriptionPrivileges::class, 'privilege_id');
    }
}
