<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'name',
        'federal_entity_id',
    ];

    /**
     * Get the municipality's name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
        );
    }

    /**
     * Get the municipality's federal entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function federalEntity()
    {
        return $this->belongsTo(FederalEntity::class);
    }

    /**
     * Get the municipality's zip codes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zipCodes()
    {
        return $this->hasMany(ZipCode::class);
    }
}
