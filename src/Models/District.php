<?php

namespace Deesynertz\Location\Models;

use Deesynertz\Location\Models\City;
use Deesynertz\Location\Models\Ward;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];

    public function getCityNameAttribute() { return $this->city->name; }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }
}
