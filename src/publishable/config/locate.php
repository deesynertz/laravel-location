<?php

return [

    'models' => [
        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your permissions. Of course, it
         * is often just the "Permission" model but you may use whatever you like.
         *
         */

        'cities' => Deesynertz\Location\Models\City::class,

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         * 
         */

        'districts' => Deesynertz\Location\Models\District::class,

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         * 
         */

        'wards' => Deesynertz\Location\Models\Ward::class,

         /*
         * When using the "HasLocations" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         * 
         */

        'streets' => Deesynertz\Location\Models\Street::class,
    ],

    'table_names' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'cities' => 'cities',

        /*
         * When using the "HasLocations" trait from this package, we need to know which
         * table should be used to retrieve your permissions. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'districts' => 'districts',

        /*
         * When using the "HasLocations" trait from this package, we need to know which
         * table should be used to retrieve your models permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'wards' => 'wards',

        /*
         * When using the "HasLocations" trait from this package, we need to know which
         * table should be used to retrieve your models roles. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'streets' => 'streets',
    ],

    'column_names' => [
        /*
         * Change this if you want to name the related pivots other than defaults
         */
        'city_pivot_key' => null, //default 'city_id',
        'district_pivot_key' => null, //default 'district_id',
        'wards_pivot_key' => null, //default 'ward_id',
        'street_pivot_key' => null, //default 'street_id',
    ],
];