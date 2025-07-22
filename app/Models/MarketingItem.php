<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingItem extends Model
{
    use HasFactory;
    public $table = "marketing";

     protected $fillable = [
        'id','title', 'url', 'media_file', 'clicks', 'price', 'status','file_type',
    ];

}
