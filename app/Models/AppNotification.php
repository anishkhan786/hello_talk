<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;
    public $table = "notifications";

    protected $fillable = [
        'id','user_id', 'type','title','body','data','channel','is_read','read_at'
    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }
}
