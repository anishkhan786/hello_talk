<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPrivileges extends Model
{
    use HasFactory;
    public $table = "subscription_privileges";

    protected $fillable = [
        'id','name','code',
    ];
}
