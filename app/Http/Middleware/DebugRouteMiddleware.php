<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugRouteMiddleware
{
  public function handle(Request $request, Closure $next)
  {
    if ($request->method() != 'GET') {
      Log::info('Route Debug', [
        'url' => $request->fullUrl(),
        'method' => $request->method(),
        'route' => $request->route() ? [
          'name' => $request->route()->getName(),
          'action' => $request->route()->getActionName(),
          'parameters' => $request->route()->parameters(),
          'middleware' => $request->route()->middleware(),
        ] : null,
        'headers' => $request->headers->all(),
        'session' => session()->all(),
        'auth' => auth()->check() ? auth()->id() : null
      ]);
    }
    error_log('Request received at earliest point: ' . $request->fullUrl());

    return $next($request);
  }
}
