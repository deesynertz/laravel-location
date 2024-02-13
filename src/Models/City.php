<?php

namespace Deesynertz\Location\Models;

use Illuminate\Database\Eloquent\Model;
use Deesynertz\Location\Models\District;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class City extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function wards(): HasManyThrough
    {
        return $this->hasManyThrough(District::class, Ward::class);
    }
}
