<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AcitivtyResource;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index(Request $request){
        $date = $request->date == null? date('Y-m-d'): $request->date;
        $activities = LogActivity::where('user_id',Auth::user()->id)->where('created_at','LIKE',"%{$date}%")->with('user')->orderBy('created_at','desc')->get();
        // dd($activities);
        return AcitivtyResource::collection($activities);
    }
}
