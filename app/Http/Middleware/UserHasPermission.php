<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\Route;

class UserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //return $next($request);

        if(Auth::user()->role == null){
            return back();
        }
        $permission = Route::currentRouteName();
        if($permission == 'logout' || $permission == 'login' || $permission == 'welcome'){
            return $next($request);
        }
        
        
        $permissionIds = Auth::user()->role->rolePermissions->pluck('permission_id')->all();
        $permissions = Permission::where('deleted',0)->whereIn('id',$permissionIds)->pluck('route')->all();

        if(!in_array($permission,$permissions)){
            return back();
        };

        return $next($request);
    }
}
