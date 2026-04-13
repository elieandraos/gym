<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = array_map(fn (string $role) => Role::from($role), $roles);

        if (! in_array($request->user()?->role, $allowedRoles)) {
            abort(403);
        }

        return $next($request);
    }
}
