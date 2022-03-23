<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use SoftDeletes;
    public $children = [];

    protected $table = 'menu_items';

    protected $fillable = [
        'name', 'link', 'menu_id', 'parent', 'access_level', 'order'
    ];

     /**
     * Scope this relation to get parent menu list.
     *
    */

    public function menu()
    {
        return $this->belongsTo('App\Models\Menu');
    }
}
