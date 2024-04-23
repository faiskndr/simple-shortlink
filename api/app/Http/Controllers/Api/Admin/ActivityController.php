<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\LogActivityExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\AcitivtyResource;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date==null? date('Y-m-d'):$request->date;
        
        $activities = LogActivity::with('user')->where('created_at','LIKE',"%{$date}%")->orderBy('created_at','desc')->paginate(5);

        return AcitivtyResource::collection($activities);
    }

    public function export() 
    {
        return Excel::download(new LogActivityExport,'log_activities.xlsx');
    }
}
