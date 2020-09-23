<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Relation extends Model
{
    public static $TYPES = ['pending', 'friendship', 'hate'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'applicant_id', 'addressee_id', 'blocker_id', 'status'
    ];

    public function applicant (): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function addressee (): BelongsTo
    {
        return $this->belongsTo(User::class, 'addressee_id');
    }

    public function blocker (): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }
}
