<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TroopersTogether extends Model
{
    use HasFactory;
    public $table = "groups";

    protected $fillable = [
        'id',
        'group_title',
        'group_description'
    ];
}
