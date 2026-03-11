<?php

namespace App\Http\Middleware;

use App\Models\User;
use Auth;
use Closure;
use Exception;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     * @throws Exception
     */
    // Routes that only require authentication, not a specific role permission
    protected array $permissionExemptRoutes = [
        'api/dashboard/notifications/count',
        'api/dashboard/notifications/unread-tickets',
        'api/dashboard/notifications/mark-read',
    ];

    public function handle($request, Closure $next)
    {
        if (strpos($request->route()->uri, 'api/dashboard') !== false) {
            if (!Auth::guard('sanctum')->check()) {
                return response()->json(['message' => __('Unauthorized')], 401);
            }
            // Skip permission check for shared dashboard routes (e.g. notifications)
            if (in_array($request->route()->uri, $this->permissionExemptRoutes, true)) {
                return $next($request);
            }
            /** @var User $user */
            $user = Auth::guard('sanctum')->user();
            $path = str_replace('\\', '.', explode('@', str_replace($request->route()->action['controller'].'\\', '', $request->route()->action['controller']))[0]);
            if (!$user->checkEffectivePermission($path)) {
                return response()->json(['message' => __('Forbidden')], 403);
            }
        }
        return $next($request);
    }
}
