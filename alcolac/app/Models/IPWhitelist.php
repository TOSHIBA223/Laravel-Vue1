<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IPWhitelist extends Model
{
    use SoftDeletes;

    protected $table = 'admin_ip_whitelist';

    protected $fillable = [
        'ip_address', 'subnet'
    ];

    protected $attributes = [
        'subnet' => 0
    ];

    /**
     * Get the model that owns the whitelist.
     */

    public static function getWhitelist()
    {
        $whitelist_objects = self::all();
        $whitelist_array = [];

        foreach( $whitelist_objects as $item ) {
            if( $item->subnet === 0 )
                $whitelist_array[] = $item->ip_address;
            else
                $whitelist_array[] = "$item->ip_address/$item->subnet";
        }

        return $whitelist_objects;
    }
}
