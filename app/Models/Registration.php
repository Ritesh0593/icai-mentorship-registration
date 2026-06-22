<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    protected $fillable = [
        'city_id',
        'name',
        'email',
        'phone',
        'resident_city',
        'participant_category',
        'mentorship_area',
        'otp_verified'
    ];

    /**
     * Get the city that this registration belongs to.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
