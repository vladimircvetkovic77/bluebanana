<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\HasChildren;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Redis;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasChildren, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'user_type',
        'email',
        'password',
    ];

    /**
     * Model events to be triggered in certain scenarios.
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // delete all redis keys which contain the user word
            // Redis::keys('user*')->each(function ($key) {
            //     Redis::del($key);
            // });
        });

        static::creating(function ($user) {
            //
        });

        static::deleted(function ($user) {
            //
        });
    }

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send the password reset notification.
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token)
    {
        $path = "Frontend_Defined_URL_And_Path";
        $url = $path."?token=".$token;
        $this->notify(new CustomResetPasswordNotification($url));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id', 'id');
    }
}
