<?php

use Illuminate\Support\Str;
use Deesynertz\Location\Models\Ward;
use Deesynertz\Location\Models\Street;
use Deesynertz\Location\Models\District;
use Deesynertz\Location\Traits\HasLocations;


// if (! function_exists('toastr')) {
//     /**
//      * Return the instance of toastr.
//      *
//      * @return Brian2694\Toastr\Toastr
//      */
//     function toastr()
//     {
//         return app('toastr');
//     }
// }

if (!function_exists('locationFinalName')) {
    function locationFinalName($string)
    {
        $name = str_replace(stringFormartForm($string), '-', ' ');

        if (Str::contains($name, 'Dar Es Salaam') || Str::contains($name, 'Dar Es Salam')) {
            if (Str::contains($name, 'Dar Es Salam')) {
                return str_replace($name, 'Dar Es Salam', 'Dar es Salaam');
            }
            return str_replace($name, 'Dar Es Salaam', 'Dar es Salaam');
        }

        return rtrim($name);
    }
}

if (!function_exists('eliminateParentNameFromChild')) {
    function eliminateParentNameFromChild($childName = null, $parents = [])
    {
        if (is_null($childName)) {
            return null;
        }

        $childName = locationFinalName($childName);

        if (!empty($parents)) {
            foreach ($parents as $parent) {
                $parent = locationFinalName($parent);
                if (Str::contains($childName, $parent)) {

                   // Check if $string1 contains $string2
                    if (strstr($childName, $parent)) {
                        // Use strstr to get the part of $string1 before $string2
                        $childName = strstr($childName, $parent, true);
                    }
                    // $childName = Str::remove($parent, $childName);
                }
            }
        }

        $childName = rtrim($childName);

        if ($childName == "") {
            return null;
        }

        return rtrim($childName);
    }
}

if (!function_exists('locationFields')) {
    function locationFields($request, $except_street = false)
    {
        $cityInput          = isset($request->city) ? $request->city : $request->_city;
        $districtInput      = isset($request->district) ? $request->district : $request->_district;

        if (isset($request->neighbourhood) || isset($request->_neighbourhood) || isset($request->ward)) {
            $neighbourhoodInput = isset($request->neighbourhood) ? $request->neighbourhood : 
                (isset($request->_neighbourhood) ? $request->_neighbourhood : $request->ward);
        }

        $coordinatesInput   = isset($request->coordinates) ? $request->coordinates : $request->_coordinate;

        $requestInputs = (object)[
            'city' => rtrim($cityInput),
            'district' => eliminateParentNameFromChild($districtInput,[$cityInput]),
            'coordinates' => $coordinatesInput,
            'neighbourhood' => eliminateParentNameFromChild($neighbourhoodInput, [$cityInput, $districtInput])
        ];

        if (!$except_street && isset($request->location_input)) {
            $requestInputs = array_merge($requestInputs, ['location_lable' => rtrim($request->location_input)]);
        }

        return $requestInputs;
    }
}

if (!function_exists('processLocationFromGoogle')) {
    function processLocationFromGoogle ($request, $fields = null, $except_street = false) 
    {
        $location = null;

        $requiredFields = [ 
            'ward_id' => null,
            'location_lable' => null
        ];

        if (is_null($fields)) {
            $fields = locationFields($request);
        }

        // if ($fields->city == 'undefined') {
            //     if ($fields->district == 'undefined') {
            //         if (!is_null(($ward = HasLocations::processLocationFromGoogle())) //handleWardUsingLike(stringFormartForm($fields->neighbourhood))->first()))) {
            //             $fields->district = $ward->district->name;
            //         }
            //     }else {
            //         if (!is_null(($district = District::where('name', 'like', '%'.stringFormartForm($fields->district).'%')->first()))) {
            //             $fields->city = $district->city->name;
            //         }

            //         if (!is_null(($ward = Ward::with('district')->where('name', 'like', '%'.stringFormartForm($fields->neighbourhood).'%')->first()))) {
            //             $fields->city  = $ward->district->city->name;
            //         }
            //     }
        // }
        $fields = (new HasLocations)->processLocationFromGoogle($fields);

       
        ## Prepare City, District & Ward
        $locationInput  = [
            'city' => stringFormartForm($fields->city),
            'district' => stringFormartForm($fields->district),
            'neighbourhood' => stringFormartForm($fields->neighbourhood),
        ];

        if (!$except_street) {
            $locationInput = array_merge($locationInput, ['location_lable' => stringFormartForm($fields->location_lable)]);
        }

        if ($fields->city !== 'undefined' && $fields->district !== 'undefined' && $fields->neighbourhood !== 'undefined') {
            $location = findOrStoreLocation($locationInput);
        }

        // if (!isset($locationInput['location_lable']) || !$location instanceof Street) {
        //     $locationInput = addElement($locationInput, locationLableHelper($request));
        // }

        if (!$except_street) {
            return locationLableHelper($requiredFields, $location);
        }

        return $location;
    }
} 

if (!function_exists('stringFormartForm')) {
    function stringFormartForm($string)
    {
        $string = getActualNull($string);
        return !is_null($string) ? Str::title($string):null;
    }
}

if (!function_exists('locationLableHelper')) {
    function locationLableHelper($request = null, $location = null)
    {
        if (!is_null($location)) {           
            if ($location instanceof Street) {
                $request = array_merge($request,[ 
                    'ward_id' => $location->ward_id,
                    'location_lable' => $location->id
                ]);

            }
        }

        return $request;
    }
}

if (!function_exists('findOrStoreLocation')) {
    function findOrStoreLocation(array $data, $callback = 'ward_id')
    {
        // $locationService = new LocationService;
        // # 01: city
        // $cityCB     = $locationService->handleFindOrStoreCity(getActualNull($data['city']));

        // # 02: using city_id to insert District
        // $districCB  = $locationService->handleFindOrStoreDistrict($cityCB, getActualNull($data['district']));

        // # 03: using district_id to findOrCreate neighbourhood
        // $hoodCB = $locationService->handleFindOrStoreNeighbourhood($districCB, getActualNull($data['neighbourhood']));

        // # 04: using ward_id to findOrCreate street
        // $results = null;

        // if (isset($data['location_lable']) && !is_null($location_lable = getActualNull($data['location_lable']))) {
        //     $results = $locationService->handleFindOrStoreStreet($hoodCB, $location_lable);
        // }

        // // if ($callback == 'ward_id') {
        // //     $results = $hoodCB;
        // // }

        // if (is_null($results)) {
        //     $results = !is_null($hoodCB) ? $hoodCB : (!is_null($districCB) ? $districCB : $cityCB);
        // }

        return (new HasLocations)->findOrStoreLocation($data, $callback);
    }
}

if(!function_exists('getActualNull')) {
    function getActualNull($stringValue) {
        return ($stringValue == 'null' || is_null($stringValue) || $stringValue == "") ? null: $stringValue;
    }
}
