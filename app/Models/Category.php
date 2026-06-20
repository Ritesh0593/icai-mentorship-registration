<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    protected $fillable = ['name'];

    /**
     * Get all cities belonging to this category.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Get all registrations for all cities in this category.
     */
    public function registrations(): HasManyThrough
    {
        return $this->hasManyThrough(Registration::class, City::class);
    }
}
