<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SKAgarwal\GoogleApi\PlacesApi;

class Address extends Model
{
    protected $table = 'user_address';
    public $timestamps = false;

    protected $fillable = [
        'address',
        'suburb',
        'state',
        'post_code',
        'country',
        'created_at',
        'updated_at',
        'user_id'
    ];

    /**
     * Scope a query to only save user address lat and lng from google places api.
     *
    */

    public function saveFromGooglePlaces(String $placeId, int $addressId, $userId)
    {
        $googlePlaces = new PlacesApi(config('constant.GoogleAI'));
        $place = $googlePlaces->placeDetails(
            $placeId,
            [
                'fields' => ['address_component', 'formatted_address']
            ]
        );

        $floor = null;
        $number = null;
        $street = null;
        $suburb = null;
        $state = null;
        $post_code = null;

        foreach($place['result']['address_components'] as $type){
            switch( $type['types'][0] )
            {
                case 'subpremise':
                    $floor = $type['long_name'] . ' ';
                    break;
                case 'street_number':
                    $number = $type['long_name'];
                    break;
                case 'route':
                    $street = $type['short_name'];
                    break;
                case 'locality':
                    $suburb = $type['long_name'];
                    break;
                case 'administrative_area_level_1':
                    $state = $type['short_name'];
                    break;
                case 'postal_code':
                    $post_code = $type['long_name'];
                    break;
            }
        }

        $save_data = [
            'address' => $floor . $number . ' ' . $street,
            'suburb' => $suburb,
            'state' => $state,
            'post_code' => $post_code,
            'country' => 'AU'
        ];

        $new_address = \DB::table($this->table)
            ->where('id', '=', $addressId)
            ->insertGetId($save_data);

        $user_update = \DB::table('users')
            ->where('id', '=', $userId)
            ->update(['address' => $new_address]);

        return $place['result']['formatted_address'];
    }

}
