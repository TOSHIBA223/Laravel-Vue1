<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SKAgarwal\GoogleApi\PlacesApi;

class Upload extends Model
{
    protected $table = 'file_upload';

    protected $fillable = [
        'path',
        'views',
        'name',
        'key',
        'archived'
    ];
    /**
     * Scope this function generate random key.
     *
    */

    public function generateKey()
    {
        $key = $this->keyStringGen();
        $does_string_exist = $this->exists($key);

        if($does_string_exist === true)
        {
            $this->generateKey();
        } else {
            return $key;
        }
    }

    /**
     * Scope this function generate ranodm key.
     *
    */

    protected function keyStringGen()
    {
        $characters = config('constant.CharaterString');
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 8; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Scope this function check exist data.
     *
    */
    public function exists($token)
    {
        $questionnaire = \DB::table($this->table)
            ->where('key', $token)
            ->get();

        if( $questionnaire->count() )
            return true;
        else
            return false;
    }

}
