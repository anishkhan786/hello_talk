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

    public function language()
    {
        return $this->belongsTo(languag::class, 'language_id');
    }

    public function settings()
    {
        return $this->hasMany(GroupSettings::class);
    }

}
