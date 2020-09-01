<?php

namespace App;

use App\Casts\Json;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Passport\HasApiTokens;

/**
 * @property mixed email_verified_at
 */
class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'social_data'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'social_data' => Json::class
    ];

    protected $appends = [
        'is_verified'
    ];

    public function getIsVerifiedAttribute (): bool
    {
        return (bool)$this->email_verified_at;
    }
}
