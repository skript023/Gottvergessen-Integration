<?php

namespace App\Http\Middleware;

use App\Models\owner;
use Closure;
use Illuminate\Http\Request;

class Hardware
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
        $user = owner::where('hardware_uuid', $request->hardware)->first();

        if (is_null($user))
        {
            if ($request->expectsJson())
            {
                return response()->json([
                    'message' => 'Injection does not allowed, potential illegal access detected.',
                ], 403);
            }
            else
            {
                abort(403);
            }
        }

        return $next($request);
    }
}
