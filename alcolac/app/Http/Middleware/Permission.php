<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Request;
use DB;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {


		 // $urle = Request::segment(2);
		 // $roleId = Auth::user()->user_role_id;

		 // if($urle == "users"){

			  // $url = $request->url();
			  // //echo "<prE>"; print_r($url); die ;

			   // $url = $request->route()->getActionName();
			   // $currenturl = explode('@',$url);
			    // $roles = DB::table('permission_roles')->where(['role_id'=>$roleId,'module_id'=>1])->first();



			// if($roles->perm_create == 0 && $currenturl == "create"){

			// // 	die('if');

              // return abort( 401 );


			// }else {

				// //die('else');
                  // return $next($request);

				 // // return abort( 401 );
			// }



		// } else {
			// return $next($request);
		 // }



		// echo "<prE>"; print_r(Auth::user()->user_role_id);

		$roleId = Auth::user()->user_role_id;



		 $roles = \DB::table('permission_roles')->where('role_id', $roleId)->get();



		//die;

        //$roles = explode(',', $request->user()->menuroles);
        //if ( ! in_array('admin', $roles) ) {
           // return abort( 401 );
        //}


		return $next($request);
    }
}
