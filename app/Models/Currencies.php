<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currencies extends Model
{
    use HasFactory;
    public $table = "currencies";

    protected $fillable = [
        'id','country_code','currency_name','currency_code', 'symbol','base_price','is_active'
    ];
}
