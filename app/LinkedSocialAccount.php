<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkedSocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider_name',
        'provider_id',
        'provider_data',
        'token'
    ];

    protected $hidden = [
        'token'
    ];

    protected $casts = [
        'provider_data' => 'json'
    ];

    public function user (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
