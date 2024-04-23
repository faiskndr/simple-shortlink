<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateRangeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Shortlink;
use App\Models\ShortlinkLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OverviewController extends Controller
{
    public function overview(Request $request)
    {
        

        $shortlink_log= ShortlinkLog::whereHas('shortlink',function($query) use($request){
            if(!$request->has('shortlink')) return $query->where('user_id',Auth::user()->id);
            return $query->where('key',$request->shortlink);
        });
        
        $shortlink_count = !$request->has('shortlink')?Shortlink::where('user_id',Auth::user()->id)->count():1;
    
        return response()->json([
            'unique_visitor'=>$shortlink_log->count(),
            'visitor'=>$shortlink_log->sum('clicked'),
            'shortlink'=>$shortlink_count
        ]);
    }

    public function overview_chart(Request $request)
    {  
        $times =[
            'day'=>"'%Y-%m-%d'",
            'week'=>"'%Y-%m-%W'",
            'month'=>"'%Y-%m'"
        ];
        $time = $times[$request->time];

        $date = DateRangeHelper::getDateRange($request->date,$time);
        $time = str_replace('W','u',$time);

        $key = $request->shortlink;
        
        $shortlink_log = ShortlinkLog::whereHas('shortlink',function($query) use ($key){
            if($key == null) return $query->where('user_id',Auth::user()->id);

            return $query->where('key',$key);
        })->whereBetween(DB::raw("DATE_FORMAT(created_at,
        $time)"),[$date[0],$date[1]])->selectRaw("COUNT(*) AS unique_visitor, SUM(clicked) as visitor,DATE_FORMAT(created_at,$time) as date")->groupByRaw("DATE_FORMAT(created_at,$time)")->get()->makeHidden('shortlink');
        

        $unique_visitor = ['data'=>[]];
        $visitor = ['data'=>[]];
        $dates = [];

        $diff =$request->time == 'week'? date_diff(date_create($date[2]),date_create($date[3])):date_diff(date_create($date[0]),date_create($date[1]));
        $diffFormat = $request->time == 'month'? '%m':'%a';
        $length = $request->time == 'week'?floor($diff->format($diffFormat)/7)+1:$diff->format($diffFormat)+1;

        for($i = 0; $i<$length; $i++)
        {
            array_push($dates,date_format(date_add(date_create($date[0]),date_interval_create_from_date_string("$i $request->time")),$request->time == 'week'?'Y-m-W':str_replace(["%","'"],'',$time)));
            $count = $shortlink_log->where('date',$dates[$i])->first();
            array_push($unique_visitor['data'],$count==null?0:$count->unique_visitor);
            array_push($visitor['data'],$count==null?0:(int)$count->visitor);
        }

        return response()->json([
            'unique_visitor'=>$unique_visitor,
            'visitor'=>$visitor,
            'date'=>$dates
        ]);
    }
}
