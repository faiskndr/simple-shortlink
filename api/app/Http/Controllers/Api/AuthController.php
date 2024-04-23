<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'=>'required',
            'username'=>'required',
            'email'=>'required',
            'password'=>'required|min:8'
        ]);

        $user_exists = User::where('email',$request->email)->orWhere('username',$request->username)->first();

        if($user_exists)
        {
            return response()->json(['message'=>'user alredy exists'],409);
        }


        $request['password'] = Hash::make($request->password);

        $user = User::create($request->all());



        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token'=>$token
        ],200);
    }

    public function currentUser(){
        return response()->json([
            'data'=>Auth::user()
        ],200);
    }


    public function login(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);
        $user = User::where('email',$request->email)->first();
    
        if($user){
            if(!$user->is_active && date_create() <= date_add($user->updated_at,date_interval_create_from_date_string('5 minute'))){
                return response()->json([
                    'message'=>'Your account is locked temporary'
                ],403);
            }
            if(!$user->is_active){
                $user->is_active = true;
                $user->failed_login = 0;
                $user->save();
            }
            if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                  
                $failed = $user->failed_login;
                $user->failed_login= $failed+=1;
                if($failed ==3){
                    $user->is_active=false;
                }
                $user->save();
                
                return response()->json([
                    'message'=>'invalid credentials'
                ],401);
            }

            $token = '';

            if($user->is_admin){
                $token=$user->createToken('auth_token',['role:admin'])->plainTextToken;
            }else{
                $token = $user->createToken('auth_token',['role:user'])->plainTextToken;
            }

            return response()->json([
                'token'=>$token
            ],200);
        }else{
            return response()->json([
                'message'=>'invalid credentials'
            ]);
        }
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'logout success'
        ]);
    }
}
