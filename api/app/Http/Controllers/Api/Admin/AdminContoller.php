<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminContoller extends Controller
{
    public function index(){
        $admins = User::where('is_admin',1)->get();

        return response()->json($admins,200);
    }

    public function store(Request $request) {
        $data = array_merge($request->all(),['is_admin'=>true]);
        $data['password'] = Hash::make($data['password']);
        
        $user = new User();
        $user->name = $data['name'];
        $user->username=$data['username'];
        $user->email=$data['email'];
        $user->password = $data['password'];
        $user->is_admin = true;
        $user->save();

        $request->merge(['message'=>"You add $request->username admin"]);
        return response()->json('ok',200);
    }

    public function removeAdmin(User $admin, Request $request) {
        $admin->delete();

        $request->merge(['message'=>"You delete $admin->username from admin"]);
        return response()->json('delete success',200);
    }
}
