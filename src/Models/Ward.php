<?php

namespace Deesynertz\Location\Models;

use Deesynertz\Location\Models\Street;
use Illuminate\Database\Eloquent\Model;
use Deesynertz\Location\Models\District;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ward extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];
    public function getDistrictNameAttribute() { return $this->district->name; }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function streets(): HasMany
    {
        return $this->hasMany(Street::class, 'ward_id', 'id');
    }
}
