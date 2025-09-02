<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class languag extends Model
{
     use HasFactory;
    public $table = "languags";

    protected $fillable = [
        'id',
        'name',
        'code',
        'flag_emoji',
        'arb_url',
    ];
}
