<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZipCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'zip_code',
        'locality',
        'municipality_id',
    ];

    /**
     * Get the zip code's locality.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function locality(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
        );
    }

    /**
     * Get the zip code's municipality.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Get all the zip code's settlements.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }
}
