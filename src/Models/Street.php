<?php

namespace Deesynertz\Location\Models;

use Deesynertz\Location\Models\Ward;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Street extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];
    public $timestamps  = false;

    public function getWardNameAttribute() { return $this->ward->name; }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }

    public function station(): HasMany
    {
        return $this->hasMany(Street::class, 'street_id', 'id');
    }
}
