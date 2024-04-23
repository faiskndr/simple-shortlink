<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ShortlinkHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShortlinkResource;
use App\Models\Microsite;
use App\Models\Shortlink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MicrositeController extends Controller
{
    public function index() 
    {
        $microsites = Shortlink::where('microsites',1)->where('user_id',Auth::user()->id)->get();
        
        return ShortlinkResource::collection($microsites);
    }


    public function edit($key) 
    {
        $microsite = Shortlink::where('key',$key)->with('microsites')->get();
        return response()->json($microsite,200);
    }

    public function store(Request $request)
    {
        try {
            $key = '';
            if(is_null($request->custom_url)){
                $createShortlink = new ShortlinkHelper();
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < 60; $i++) {
                    $randomString .= $characters[random_int(0, $charactersLength - 1)];
                }
                
                while (true) {
                
                    $key = $createShortlink->createToken($randomString);

                    if(!Shortlink::where('key',$key)->first()){
                        break;
                    }
                }
            }else{
                $key= $request->custom_url;
            }

            $microsites = new Shortlink();
            $microsites->key = $key;
            $microsites->password = $request->password!=null?base64_encode($request->password):null;
            $microsites->user_id = Auth::user()->id;
            $microsites->microsites = true;
            $microsites->save();

            return response()->json(['key'=>$key]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function updateMicrosite(Request $request) 
    {
        // try {
            $shortlink = Shortlink::where('key',$request->key)->firstOrFail();
            
            $links = $request->links;
            // dd($links);
            // $microsite = new Microsite();
            foreach ($links as $link) {
                Microsite::create([
                    'shortlink_id'=>$shortlink->id,
                    'long_url'=>$link['long_url']
                ]);    
                // dd($link);
            }
            
            return response()->json(['message'=>'ok'],200);
        // } catch (\Throwable $th) {
            //throw $th;
        // }
    }
}
