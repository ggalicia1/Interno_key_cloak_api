<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ClientKeyCloakMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (session()->has('user')) {
                $user = session()->get('user');
                if(($user['client_id'] == 'sso-client')){
                    foreach ($user['resource_access'] as $key => $value) {
                        if($key == 'sso-client'){
                            return $next($request);
                        }
                    }

                    return response()->json([
                        'status'   => false,
                        'message' => 'No autorizado.',
                        'error' => 'El cliente no tiene acceso.'
                    ], Response::HTTP_UNAUTHORIZED);
                }
                return response()->json([
                            'status'   => false,
                            'message' => 'Cliente no autorizado.',
                            'error' => null
                        ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                            'status'   => false,
                            'message' => 'No autorizado.',
                            'error' => null
                        ], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $th) {
            Log::error('El cliente no tiene acceso: ' . $th->getMessage());
            return response()->json([
                'status'   => false,
                'message' => 'No autorizado.',
                'error' => 'El cliente no tiene acceso.'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
