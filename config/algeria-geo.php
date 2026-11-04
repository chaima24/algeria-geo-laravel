<?php

return [

    /*
    | The model classes the package uses. To add your own relations, extend a
    | package model in your app and put your class here.
    */
    'models' => [
        'region' => Chaima24\AlgeriaGeo\Models\Region::class,
        'wilaya' => Chaima24\AlgeriaGeo\Models\Wilaya::class,
        'daira' => Chaima24\AlgeriaGeo\Models\Daira::class,
        'commune' => Chaima24\AlgeriaGeo\Models\Commune::class,
    ],

];
