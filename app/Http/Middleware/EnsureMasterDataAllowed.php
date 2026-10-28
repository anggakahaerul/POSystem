<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMasterDataAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userRoles = $user->roles->pluck('name');
        $hasAccess = $userRoles->intersect(['Admin', 'Administrator', 'HR'])->isNotEmpty();

        if (!$hasAccess) {
            abort(403, 'Akses Master Data hanya diperuntukkan bagi HR dan Administrator.');
        }

        return $next($request);
    }
}
