<?php

namespace Deesynertz\Location\Traits;

use Deesynertz\Location\Models\Ward;
use Deesynertz\Location\Models\District;




trait HasLocations
{
    use StreetTrait;

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

    public function processLocationFromGoogle($fields) {

        if ($fields->city == 'undefined') {
            if ($fields->district == 'undefined') {
                if (!is_null(($ward = $this->handleWardUsingLike(stringFormartForm($fields->neighbourhood))->first()))) {
                    $fields->district = $ward->district->name;
                }
            }else {
                if (!is_null(($district = District::where('name', 'like', '%'.stringFormartForm($fields->district).'%')->first()))) {
                    $fields->city = $district->city->name;
                }

                if (!is_null(($ward = Ward::with('district')->where('name', 'like', '%'.stringFormartForm($fields->neighbourhood).'%')->first()))) {
                    $fields->city  = $ward->district->city->name;
                }
            }

        }

        return $fields;
    }

    public function findOrStoreLocation(array $data, $callback = 'ward_id') {

        # 01: city
        $cityCB     = $this->handleFindOrStoreCity(getActualNull($data['city']));

        # 02: using city_id to insert District
        $districCB  = $this->handleFindOrStoreDistrict($cityCB, getActualNull($data['district']));

        # 03: using district_id to findOrCreate neighbourhood
        $hoodCB = $this->handleFindOrStoreNeighbourhood($districCB, getActualNull($data['neighbourhood']));

        # 04: using ward_id to findOrCreate street
        $results = null;

        if (isset($data['location_lable']) && !is_null($location_lable = getActualNull($data['location_lable']))) {
            $results = $this->handleFindOrStoreStreet($hoodCB, $location_lable);
        }

        // if ($callback == 'ward_id') {
        //     $results = $hoodCB;
        // }

        if (is_null($results)) {
            $results = !is_null($hoodCB) ? $hoodCB : (!is_null($districCB) ? $districCB : $cityCB);
        }

        return $results;

    }


    
}