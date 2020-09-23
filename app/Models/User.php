<?php

namespace App\Models;

use App\Casts\Json;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Passport\HasApiTokens;

/**
 * @property mixed email_verified_at
 * @property mixed applicantRelations
 * @property mixed addresseeRelations
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
        'name', 'email', 'password', 'avatar', 'social_data', 'email_verified_at'
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

    protected static function boot (): void
    {
        parent::boot();
        static::observe(UserObserver::class);
    }

    public function linkedSocialAccounts (): HasMany
    {
        return $this->hasMany(LinkedSocialAccount::class);
    }

    public function applicantRelations (): HasMany
    {
        return $this->hasMany(Relation::class, 'applicant_id');
    }

    public function addresseeRelations (): HasMany
    {
        return $this->hasMany(Relation::class, 'addressee_id');
    }

    public function blockerRelations (): HasMany
    {
        return $this->hasMany(Relation::class, 'blocker_id');
    }

    public function relations ()
    {
        return $this->applicantRelations->merge($this->addresseeRelations);
    }

    public function getIsVerifiedAttribute (): bool
    {
        return (bool)$this->email_verified_at;
    }
}
