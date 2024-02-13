<?php

namespace Deesynertz\Location\Traits;

use Deesynertz\Location\Models\City;
use Deesynertz\Location\Models\District;
use Deesynertz\Location\Traits\CityTrait;


trait DistrictTrait
{
    use CityTrait;
    
    public function handleDistrictList()
    {
        return District::with(['city', 'wards']);
    }

    public function handleDistrictByID(int $id)
    {
        return District::with(['city', 'wards'])->findOrFail($id);
    }

    public function handleCreateDistrict(array $data)
    {
        return District::create([
            'city_id' => $data["city_id"],
            'name'    => locationFinalName($data["district_name"])
        ]);
    }

    public function handleFindOrStoreDistrict(City $city = null, $distictName = null)
    {
        if (!is_null($city)) {
            $district = ['city_id' => $city->id, 'name' => locationFinalName($distictName)];
            return is_null($distictName) ? null : District::firstOrCreate($district, $district);
        }

        return null;
    }

    public function updateDistrictsAsLocationsNames()
    {
        try {
            $countRows = 0;

            $allDistricts = District::all();

            foreach ($allDistricts as $district) {
                if (($newName = locationFinalName($district->name)) !== $district->name) {
                    $countRows += 1;
                    $district->update(['name' => $newName]);
                }
            }

            return $countRows . ' Records affected on the table "districts"  executed on ' . today();
        } catch (\Throwable $th) {
            $message = 'Error occured command "districts" executed on ' . today() . ' Message: ' . $th->getMessage();
            return $message;
        }
    }

    public function randomizeDistrict()
    {
        return $this->handleDistrictList()->withCount('wards')->whereHas('wards', function($wards) {
            $wards->has('broadcastAgents');
        })->orderBy('wards_count', 'ASC');
    }

}