<?php

namespace Deesynertz\Location\Traits;

use Deesynertz\Location\Models\Ward;
use Deesynertz\Location\Models\District;
use Deesynertz\Location\Traits\DistrictTrait;


trait WardTrait
{
    use DistrictTrait;
    
    public function handleWardList()
    {
        return Ward::with(['district.city', 'broadcastAgents']);
    }

    public function handleWardByID(int $id)
    {
        return Ward::with(['district', 'broadcastAgents'])->findOrFail($id);
    }

    public function handleWardUsingLike($string)
    {
        $likesVariable = '%'. $string.'%';
        return Ward::with('district')->where('name', 'like', $likesVariable);
    }

    public function handleWardByDistrictId(int $districtId)
    {
        return Ward::with(['district', 'houses', 'broadcastAgents'])->where('district_id', $districtId);
    }

    public function handleCreateWard(array $data)
    {
        return Ward::create([
            'district_id' => $data["district_id"],
            'name'        => locationFinalName($data["ward_name"])
        ]);
    }

    public function handleFindOrStoreNeighbourhood(District $district = null, $name = null)
    {
        if (!is_null($district)) {
            $neighbourhood = ['district_id' => $district->id, 'name' => locationFinalName($name)];
            return is_null($name) ? null : Ward::firstOrCreate($neighbourhood, $neighbourhood);
        }

        return null;
    }

}