<?php

namespace App\Http\Middleware;

use App\Models\Member;
use App\Models\User;
use App\Utils\BasicAuth;
use Closure;

class WebMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  $roleID
     * @return mixed
     */
    public function handle($request, Closure $next, $roleID)
    {
        if (!$request->hasHeader('Authorization')) {
            return response('token not provided', 401);
        }
        if ($roleID == 1) {
            $user = User::where('token', $request->header('Authorization'))->first();
            if (!$user) {
                return response('token invalid', 401);
            }
            BasicAuth::getInstance()->setModel($user);
            return $next($request);
        }
        if ($roleID == 2) {
            $member = Member::where('token', $request->header('Authorization'))->first();
            if (!$member) {
                return response('token invalid', 401);
            }
            BasicAuth::getInstance()->setModel($member);
            return $next($request);
        }
    }
}
