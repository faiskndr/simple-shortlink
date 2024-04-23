<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\DateRangeHelper;
use App\Http\Controllers\Controller;
use App\Models\Shortlink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {   

        $date = DateRangeHelper::getDateRange($request->date);
        
        $diff = date_diff(date_create($date[0]),date_create($date[1]));

        $users = User::where('is_admin',false)
                 ->whereBetween(DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'),[$date[0],$date[1]])
                 ->select(DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d") as date'),DB::raw('COUNT(*) as users'))
                 ->groupBy('created_at')
                 ->get();
        $countUsers = User::where('is_admin',false)->count();
        $data = ['date'=>[],'data'=>[],'count'=>$countUsers];
                
       
    
            
            for($i = 0; $i<$diff->format('%a')+1; $i++)
            {
                array_push($data['date'],date_format(date_add(date_create($date[0]),date_interval_create_from_date_string("$i day")),'Y-m-d'));
                $count = $users->where('date',$data['date'][$i])->first();
                array_push($data['data'],$count == null?0:$count->users);
            }
            
        return response()->json($data);
    }

    public function shortlinks(Request $request)
    {
        
        $times =[
            'day'=>"'%Y-%m-%d'",
            'week'=>"'%Y-%m-%W'",
            'month'=>"'%Y-%m'"
        ];
        $time = $times[$request->time];
        $date = DateRangeHelper::getDateRange($request->date,$time);
        $time = str_replace('W','u',$time);
        $diff =$request->time == 'week'? date_diff(date_create($date[2]),date_create($date[3])):date_diff(date_create($date[0]),date_create($date[1]));
        $shortlinks = Shortlink::whereBetween(DB::raw("DATE_FORMAT(created_at,
        $time)"),[$date[0],$date[1]])->selectRaw("DATE_FORMAT(created_at,$time) as date, COUNT(*) as data")
        ->groupBy(DB::raw("DATE_FORMAT(created_at,$time)"))
        ->get();
        
        $countShortlink = Shortlink::count();
        $data = ['date'=>[],'data'=>[],'count'=>$countShortlink];
        $diffFormat = $request->time == 'month'? '%m':'%a';
        $length = $request->time == 'week'?floor($diff->format($diffFormat)/7)+1:$diff->format($diffFormat)+1;
        // dd($length);
            for($i=0;$i<$length; $i++)
            {
                array_push($data['date'],date_format(date_add(date_create($date[0]),date_interval_create_from_date_string("$i $request->time")),$request->time == 'week'?'Y-m-W':str_replace(["%","'"],'',$time)));
                $count = $shortlinks->where('date',$data['date'][$i])->first();
                array_push($data['data'],$count == null?0:$count->data);
            }
            
            
        return response()->json($data,200);
    }
}
