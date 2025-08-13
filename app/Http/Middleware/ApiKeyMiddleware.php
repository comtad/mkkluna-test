<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->query('api_key');

        if (!$apiKey || $apiKey !== env('API_KEY')) {
            return response()->json([
                'error' => 'Неверный или отсутствующий API ключ'
            ], 401);
        }

        return $next($request);
    }
}