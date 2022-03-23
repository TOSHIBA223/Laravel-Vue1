<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{

    use SoftDeletes;

    protected $table = 'menus';

    protected $fillable = ['name'];

    /**
     * Scope this relation to get menu item list.
     *
    */

    public function menuItems()
    {
        return $this->hasMany('App\Models\MenuItem');
    }
}
