<?php

namespace App\Http\Middleware;

use App\Models\Shortlink;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShortlinkToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $shortlink = Shortlink::where('key',$request->id)->first();
        // dd($request['token']);
        if($shortlink){
        
            if(!$shortlink->hasToken($request['token'])){
                return response()->json([
                    'message'=>'unathorized'
                ],401);
            }

            if(!$shortlink->validateToken(base64_encode($request['token']))){
                return response()->json([
                    'message'=>'invalid token'
                ],401);
            }

            return $next($request->merge(['id'=>$shortlink->id,'long_url'=>$shortlink->long_url]));;
        }
        return response()->json([
            'message'=>'not found'
        ],404);
    }
}
