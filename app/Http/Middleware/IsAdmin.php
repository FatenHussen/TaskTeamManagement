<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;

class IsAdmin // Class name should match the file name (IsAdmin)
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return $this->unauthorizedResponse();
        }

        $user = Auth::user(); // Get the authenticated user

        // Check if the authenticated user is an admin
        if ($user->is_admin) {
            return $next($request); // Allow the request to proceed if the user is an admin
        }

        // If the user is not an admin, return an unauthorized response
        return $this->unauthorizedResponse();
    }

    /**
     * Return a standard unauthorized response.
     *
     * @return \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function unauthorizedResponse()
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Unauthorized. Admin access only.',
        ], 403));
    }
}
