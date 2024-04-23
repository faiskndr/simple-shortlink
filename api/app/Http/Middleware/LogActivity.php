<?php

namespace App\Http\Middleware;

use App\Helpers\LogMessagesHelper;
use App\Models\LogActivity as ModelsLogActivity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response)
    {
        // $message = new LogMessagesHelper();        
        $action = explode('/',$request->path());
        if(Auth::user()){
            ModelsLogActivity::create([
                'user_id'=>Auth::user()->id,
                // 'activity'=>$request->hasAny('message')?$request->message:$message->getMessage($request->path(),$request->method(),$request->all()),
                'action'=>$action[sizeof($action)-1]
            ]);
        }

        // return response()->json($message->getMessage($request->path(),$request->method()),200);
    }
}
