<?php

namespace App\Http\Middleware;

use App\Models\ShortlinkLog as ModelsShortlinkLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShortlinkLog
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
        $shortlinkLog = ModelsShortlinkLog::where('shortlink_id',$request->id)->where('user_id',$request->user_id)->where('user_agent',$request->header('user_agent'))->orderBy('id','desc')->first();
   
        if($shortlinkLog!=null && date_format(date_create($shortlinkLog->created_at),'Y-m-d')==date_format(date_create(),'Y-m-d')){
            $shortlinkLog->clicked+=1;
            $shortlinkLog->save();
        }else{
            ModelsShortlinkLog::create([
                'shortlink_id'=>$request->id,
                'user_id'=>$request->user_id,
                'clicked'=>1,
                'user_agent'=>$request->header('user_agent')
            ]);
        }
    }
}
