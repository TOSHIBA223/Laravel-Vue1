<?php
namespace App\Services;

use Illuminate\Http\Request;
use SKAgarwal\GoogleApi\PlacesApi;

class GooglePlaces {

    public static function findAddress($address)
    {
        $googlePlaces = new PlacesApi('AIzaSyDheZKX2UPKow1dTIkaCYUAXUAqzGyZrso');
        $general =  $googlePlaces->placeAutocomplete($address,
            [
                'components' => 'country:au'
            ]);
        $details = [];

        $i = 0;
        foreach($general['predictions'] as $location)
        {
            $details[$i]['address'] = $googlePlaces->placeDetails(
                $location['place_id'],
                [
                    'fields' => 'formatted_address'
                ]
            );
            $details[$i]['place_id'] = $location['place_id'];
            $i++;
        }

        return $details;
    }
}
