<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        if ($request->getContent() && !str_contains($request->headers->get('Content-Type', ''), 'application/json')) {
            $request->headers->set('Content-Type', 'application/json');
        }

        return $next($request);
    }
}
