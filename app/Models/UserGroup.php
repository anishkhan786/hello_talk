<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;
    public $table = "user_groups";

    protected $fillable = [
        'id',
        'user_id',
        'group_id',
        'created_at',
        'updated_at',
    ];
      // Relation with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relation with Group (optional)
    public function group()
    {
        return $this->belongsTo(TroopersTogether::class, 'group_id', 'id');
    }
}
