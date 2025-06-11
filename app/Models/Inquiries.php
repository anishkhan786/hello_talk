<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiries extends Model
{
    use HasFactory;
    public $table = "inquiries";

    protected $fillable = [
        'id',
        'user_id',
        'phone',
        'subject',
        'message',
        'status'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
