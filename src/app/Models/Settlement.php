<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
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
        'zone_type',
        'settlement_type_id',
        'zip_code_id',
    ];

    /**
     * Get the settlement's name.
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
     * Get the settlement's zone type.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function zoneType(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
        );
    }

    /**
     * Get the settlement's type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function settlementType()
    {
        return $this->belongsTo(SettlementType::class);
    }

    /**
     * Get the settlement's zip code.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zipCode()
    {
        return $this->belongsTo(ZipCode::class);
    }
}
