<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index() {
        $users = User::where('is_admin',0)->withCount('shortlinks')->get();
        return response()->json([
            'data'=>$users
        ],200);
    }

    public function show($username){
        $user = User::where('is_admin',0)->where('username',$username)->first();

        return response()->json([
            $user
        ],200);
    }

    public function export() 
    {
        return Excel::download(new UserExport,'users.xlsx');
    }


    public function verifyEmail(Request $request){
        // dd($request->all());
        if(!$request->json('verify')){
            return response()->json([
                'error'=>'invalid input'
            ],422);
        }
        $date = date('Y-m-d',time());
        User::where('username',$request->username)->first()->update(['email_verified_at'=>$date]);

        $data = array_merge($request->all(),['time'=>$date]);
        return response()->json($data);
    }
}
