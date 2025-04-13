<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Penduduk;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;

class ApiTokenOwnerMiddleware
{
   
    public function handle(Request $request, Closure $next): Response
    {
        
        $bearerToken = $request->bearerToken();

        // If no token is provided, proceed but token owner will be null
        if (!$bearerToken) {
            Log::info('API request without bearer token');
            return $next($request);
        }

        try {
            // Find the token hash in the database
            $tokenHash = hash('sha256', $bearerToken);
            $token = PersonalAccessToken::where('token', $tokenHash)->first();

            if (!$token) {
                Log::warning('Invalid API token provided');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid API token'
                ], 401);
            }

            // Get the token owner details
            $tokenable = $token->tokenable;

            // Identify if token belongs to User or Penduduk
            if ($tokenable instanceof User) {
                Log::info('API token owner identified: User', [
                    'user_id' => $tokenable->id,
                    'nik' => $tokenable->nik,
                    'username' => $tokenable->username,
                    'role' => $tokenable->role,
                ]);

                // Add token owner info to the request
                $request->attributes->add([
                    'token_owner' => $tokenable,
                    'token_owner_type' => 'user',
                    'token_owner_role' => $tokenable->role,
                    'token' => $token
                ]);

            } elseif ($tokenable instanceof Penduduk) {
                Log::info('API token owner identified: Penduduk', [
                    'penduduk_id' => $tokenable->id,
                    'nik' => $tokenable->nik,
                ]);

                // Add token owner info to the request
                $request->attributes->add([
                    'token_owner' => $tokenable,
                    'token_owner_type' => 'penduduk',
                    'token_owner_role' => 'user',
                    'token' => $token
                ]);

            } else {
                Log::warning('Unknown token owner type', [
                    'tokenable_type' => get_class($tokenable),
                    'tokenable_id' => $tokenable->id
                ]);
            }

            // Update last used timestamp
            $token->last_used_at = now();
            $token->save();

        } catch (\Exception $e) {
            Log::error('Error in API token identification: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }

        return $next($request);
    }
}
