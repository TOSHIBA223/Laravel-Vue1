<?php
namespace App\Http\Middleware;

use App\Models\Menu;
use App\Models\MenuItem;
use Closure;

class PageAccessPermission
{

    public function handle($request, Closure $next)
    {
        if (\Auth::check()) {
		//	dd( \Auth::user()->role);
           // $user_permission_level = \Auth::user()->role->menu_permission_level;
            $current_url = \Str::after(url()->current(), \URL::to('/') . '/admin/');
            $current_page_item = MenuItem::where('link', '=', $current_url)->get();
          //  $indexed_page_item = array_values($current_page_item);

          //  if (isset($indexed_page_item[0]) && $user_permission_level >= $indexed_page_item[0]['access_level'])
                return $next($request);
        }

        return back()->withErrors(['access' => 'Access Denied!! ' . $request->method()]);
    }
}
