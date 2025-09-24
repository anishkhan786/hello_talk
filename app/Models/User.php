<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use SoftDeletes, HasFactory, Notifiable ,HasApiTokens;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone_no',
        'password',
        'social_login_type',
        'type',
        'source',
        'introduction',
        'profession',
        'personality',
        'interest',
        
        'avatar',
        'native_language',
        'learning_language',
        'know_language',
        'country',
        'dob',
        'gender',
        'is_active',
        'is_banned',
        'last_seen',
        'remember_token',
        'fcm_token',
        'data_deletion',
        'data_deletion_date',
        'multimedia',
        'translate_language',
        'interface_language',
        'online_status',
        'location',
        'notification',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function countryDetail()
    {
        return $this->belongsTo(contry::class, 'country', 'name');
    }

    public function nativeLanguageDetail()
    {
        return $this->belongsTo(languag::class, 'native_language', 'name');
    }

     public function knowLanguageDetail()
    {
        return $this->belongsTo(languag::class, 'know_language', 'name');
    }

    public function learningLanguageDetail()
    {
        return $this->belongsTo(languag::class, 'learning_language', 'name');
    }

            // Who follows me
    public function followers()
        {
            return $this->hasMany(Follow::class, 'following_id');
        }

        // Who I follow
    public function favorites()
        {
            return $this->hasMany(Follow::class, 'follower_id');
        }

    public function posts()
        {
            return $this->hasMany(Posts::class); // assuming model name is Post
        }

}
