<?php

namespace App\Http\Middleware;

use App\Helpers\JwtToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class JwtTokenVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            $token = $request->cookie('token');

            if (!$token) {
                $authHeader = $request->header('Authorization');
                if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                    $token = $matches[1];
                }
            }

             // If no token at all → unauthenticated
            if (!$token) {
                return $this->kickToLogin($request, 'Unauthenticated');
            }

            $decode = JwtToken::verifyToken($token);

            // Treat any verify error as unauthenticated (incl. expired)
            if (!empty($decode['error'])) {
                $msg = $decode['message'] ?? 'Unauthenticated or token expired';
                return $this->kickToLogin($request, $msg);
            }
            
            // if(!$token){
            //     if($request->expectsJson()){
            //         return response()->json([
            //             'message' => 'Unauthenticated',
            //         ],401);
            //     }
            //     return redirect()->route('login');
            // }

            
            // if($decode['error']){
            //     return response()->json([
            //         'message' => 'Something went wrong',
            //     ]);
            // }

            $payload = $decode['payload'] ?? null;

            // 4) Extra safety: check exp in middleware if present
            if (!$payload || (isset($payload->exp) && $payload->exp < time())) {
                return $this->kickToLogin($request, 'Token expired');
            }
            
            $user = User::where('id', $payload->id)
                ->where('email', $payload->email)
                ->first();

                if (!$user) {
                return response()->json(['message' => 'User not found'], 401);
            }

            Auth::setUser($user);

            return $next($request);
        }catch(\Exception $e){
            Log::critical($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            return response()->json([
                'message' => 'Something went wrong',
            ]);
        }
    }

    private function kickToLogin(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 401)
                ->withCookie(cookie()->forget('token'));
        }

        return redirect()->route('login')
            ->with('error', $message)
            ->withCookie(cookie()->forget('token'));
    }
}
