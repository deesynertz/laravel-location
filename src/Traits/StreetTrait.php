<?php

namespace Deesynertz\Location\Traits;

use Deesynertz\Location\Models\Ward;
use Deesynertz\Location\Models\Street;
use Deesynertz\Location\Traits\WardTrait;


trait StreetTrait
{
    use WardTrait;
    
    public function handleStreetList()
    {
        return Street::get();
    }

    public function getStreetById($stationId)
    {
        return Street::with('ward')->find($stationId);
    }

    public function handleFindOrStoreStreet(Ward $ward = null, $name = null)
    {
        if (!is_null($ward)) {
            $neighbourhood = ['ward_id' => $ward->id, 'name' => locationFinalName($name)];
            return is_null($name) ? null : Street::firstOrCreate($neighbourhood, $neighbourhood);
        }

        return null;
    }

    public function updateStationsAsLocationsNames()
    {
        try {
            $countRows = 0;

            $allStations = Street::all();

            foreach ($allStations as $station) {
                if (($newName = locationFinalName($station->name)) !== $station->name) {
                    $countRows += 1;
                    $station->update(['name' => $newName]);
                }
            }

            return $countRows . ' Records affected on the table "stations"  executed on ' . today();
        } catch (\Throwable $th) {
            $message = 'Error occured command "stations" executed on ' . today() . ' Message: ' . $th->getMessage();
            return $message;
        }
    }
}