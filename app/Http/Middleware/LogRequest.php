<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequest
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
		$response = $next($request);

		if(auth()->user()) {
            $log = [
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'method' => $request->getMethod(),
				'agent' => $request->header('user-agent'),
                'url' => request()->fullUrl(),
                'request_body' => $request->except(['password', 'password_confirmation']),
            ];

            Log::channel('request')->info(json_encode($log));
        }

        return $response;
    }
}
