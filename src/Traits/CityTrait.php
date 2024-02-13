<?php

namespace Deesynertz\Location\Traits;

use Illuminate\Http\Request;
use Deesynertz\Location\Models\City;
use Deesynertz\Location\Models\Ward;


trait CityTrait
{
    /**
     * Scope a query to include records within the specified radius from a given latitude and longitude.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $latitude
     * @param float $longitude
     * @param float $radius
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithinRadius($query, $latitude, $longitude, $radius)
    {
        // Calculate the distance using the Haversine formula
        $earthRadius = 6371; // Earth's radius in kilometers
        $distanceExpression = "( $earthRadius * acos( cos( radians( $latitude ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( $longitude ) ) + sin( radians( $latitude ) ) * sin( radians( latitude ) ) ) )";

        // Add a where clause to filter records within the specified radius
        return $query->whereRaw("$distanceExpression < $radius");
    }


    public function handleCityList()
    {
        return City::with('district');
    }

    public function handleCityByID(int $id)
    {
        return City::findOrFail($id);
    }

    public function handleCreateCity(array $data)
    {
        return $this->handleFindOrStoreCity($data["city_name"]);

        // do {
        //     $cityName = locationFinalName($data["city_name"]);
        //     $abbr = '';
        //     for ($i = 0; $i < 3; $i++) {
        //         $abbr .= $cityName[rand(0, strlen($cityName) - 1)];
        //     }
        // } while (City::where('abbr', $abbr)->first());
        // return City::create(['name' => locationFinalName($cityName), 'abbr' => Str::upper($abbr)]);
    }

    public function handleFindOrStoreCity($city)
    {
        $firstOr = ['name' => locationFinalName($city)];
        $createInfo = $city;

        if (!is_null($abbr = $this->getSuggestedABR($city))) {
           $createInfo = array_merge($firstOr, ['abbr' => $abbr]);
        }
        return City::firstOrCreate($firstOr, $createInfo);
    }


    public function getSuggestedABR($city)
    {
        $abbrs = [
            'Arusha'         =>     'ARU',
            'Dar es Salaam'  =>     'DAR',
            'Dodoma'         =>     'DOD',
            'Geita'          =>     'GEI',
            'Iringa'         =>     'IRI',
            'Kagera'         =>     'KAG',    //  [West Lake]	Reg	Bukoba
            'Katavi'         =>     'KAT',    //  Reg	Mpanda
            'Kigoma'         =>     'KIG',    //  Reg	Kigoma
            'Kilimanjaro'    =>     'KIL',    //  Reg	Moshi
            'Lindi'          =>     'LIN',
            'Manyara'        =>     'MAY',    // Reg	Babati
            'Mara'           =>     'MAR',    // Reg	Musoma
            'Mbeya'          =>     'MBE',
            'Morogoro'       =>     'MOR',
            'Mtwara'         =>     'MTW',
            'Mwanza'         =>     'MWA',
            'Njombe'         =>     'NJO',
            'Pwani'          =>     'PWA',
            'Rukwa'          =>     'RUK',    //  Reg	Sumbawanga
            'Ruvuma'         =>     'RUV',    //  Reg	Songea
            'Shinyanga'      =>     'SHI',    //  Reg	Shinyanga
            'Simiyu'         =>     'SIM',    //  Reg	Bariadi
            'Singida'        =>     'SIN',
            'Songwe'         =>     'SON',    //  Reg	Vwawa
            'Tabora'         =>     'TAB',
            'Tanga'          =>     'TAN',    //  Reg	Tanga
            'Zanzibar'       =>     'ZAN',
        ];

        foreach ($abbrs as $key => $value) {
            if (locationFinalName($key) == locationFinalName($city)) {
                return $value;
            }
        }

        return null;
    }

    public function propertLocationPoint(Request $request, $except_street = false)
    {
        // if ($request->station_pickup > 0) {
        //     return [
        //         'location_lable' => (int)$request->station_pickup,
        //     ];
        // }

        $fields = locationFields($request, $except_street);
        $locationCreated = processLocationFromGoogle($request, $fields, $except_street);

        if (!$except_street) {
            ## Prepare Coordinates
            $coordValue    = explode(', ', $fields->coordinates);
    
            ## prepared_Value
            return array_merge($locationCreated, [
                'latitude'  => (!is_null($coordValue[0])) ? $coordValue[0] : null,
                'longitude' => (!is_null($coordValue[count($coordValue) - 1])) ? $coordValue[count($coordValue) - 1] : null,
            ]);
        }

        return $locationCreated;        
    }

    public function updateLocationsNames()
    {
        // $message = '========== ** '.today(). ' ** ==========> ';
        $message = $this->updateCilitiesAsLocationsNames();
        $message .= ' & '.  $this->updateDistrictsAsLocationsNames();
        $message .= ' & ' . $this->updateWardsAsLocationsNames();
        $message .= ' & ' . $this->updateStationsAsLocationsNames();
        return ($message);
    }

    public function updateCilitiesAsLocationsNames()
    {
        try {
            $countRows = 0;
            $updateActionDone = false;

            $allCities = City::all();

            foreach ($allCities as $city) {
                $newInfo = [
                    'name' => $city->name,
                    'abbr' => $city->abbr
                ];

                if (($newName = locationFinalName($city->name)) !== $city->name) {
                    $newInfo['name'] = $newName;
                    $updateActionDone = true;
                }

                if ($city->abbr !== ($newAbbr = $this->getSuggestedABR($newInfo['name']))) {
                    $newInfo['abbr'] = $newAbbr;
                    $updateActionDone = true;
                }

                if($updateActionDone) {
                    $city->update($newInfo);
                    $countRows += 1;
                }
            }

            return $countRows . ' Records affected on the table "cities"  executed on ' . today();

        } catch (\Throwable $th) {
            $message = 'Error occured command "cities" executed on ' . today() . ' Message: ' . $th->getMessage();
            return $message;
        }
    }



    public function updateWardsAsLocationsNames()
    {
        try {
            $countRows = 0;

            $allWards = Ward::all();

            foreach ($allWards as $ward) {
                if (($newName = locationFinalName($ward->name)) !== $ward->name) {
                    $countRows += 1;
                    $ward->update(['name' => $newName]);
                }
            }

            return $countRows . ' Records affected on the table "wards"  executed on ' . today();
        } catch (\Throwable $th) {
            $message = 'Error occured command "wards" executed on ' . today() . ' Message: ' . $th->getMessage();
            return $message;
        }
    }

    public function finalizeLocationSetUp($request, $values)
    {
        // $update = true;
        // if ($request->type == 'House') {
        //     $update = $request->target->update($values);
        // }
    
        // if ($request->type == 'ProjectUnits') {
        //     $update = $request->target->update($values);
        // }
    
        // if ($request->type == 'Land') {
        //     $land = $request->target;
        //     $update = $land->update($values);
        //     if (isset($values['ward_id'])) {
        //         $land->landDetails->update($values);
        //     }
        // }

        // return ($update) ? $this->removerificationCode($request->target) : false;
    }

    protected function removerificationCode($target)
    {
        return $target->gateable->delete();
    }
    
}