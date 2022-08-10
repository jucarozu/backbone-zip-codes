<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettlementType extends Model
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
    ];

    /**
     * Get all the settlements' types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }
}
