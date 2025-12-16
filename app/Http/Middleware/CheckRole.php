<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$types)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login'); // Or abort(401)
        }
        
        // Get UserTypeID as integer to avoid object conversion issues
        // Use the accessor which should return integer, but add safety check
        $userTypeId = $user->getUserTypeId();
        
        if (!$user || !in_array($userTypeId, array_map('intval', $types))) {
             return redirect()->back()->with('error', 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }
}
