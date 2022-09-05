<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $userId = auth()->user()->id;
        $user = User::where('id', $userId)->first();

        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }
        abort(403);
    }
}
