<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class CustomAction
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

        // current route
        $GLOBALS['current_route_name'] = isset(request()->route()->action['as']) ? request()->route()->action['as'] : "";

        // general do_action
        pathToAction();

        // add old value to json 
        $old_input_value = session()->get('_old_input');
        if ($old_input_value) {
            $GLOBALS['data_page'] = $old_input_value;
        }

        // if /public redirect
        $res = fixPublicLaravel();
        if (is_object($res)) {
            return $res;
        }

        // user data
        if (hasTable("users")) {
            $GLOBALS['user'] = auth()->user();


            // if /dashboard user middleware
            if (isDashboard()) {
                $userActionsRes = userActionsMiddleware();
                if (is_object($userActionsRes)) return $userActionsRes;
            }else{
                do_action("front_end_started");
            }
        }

        return $next($request);
    }
}
