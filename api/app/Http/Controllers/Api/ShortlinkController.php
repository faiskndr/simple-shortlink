<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateRangeHelper;
use App\Helpers\ShortlinkHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShortlinkResource;
use App\Models\Shortlink;
use App\Models\ShortlinkLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use chillerlan\QRCode\{QRCode,QROptions};
use chillerlan\QRCode\Output\QROutputInterface;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\ShortlinkLog as ModelsShortlinkLog;


class ShortlinkController extends Controller
{
    public function redirectTo(Request $request){
        
        if($request->bearerToken()!=null){
            $user = PersonalAccessToken::findToken($request->bearerToken());
            $request->merge(['user_id'=>$user->tokenable->id]);
        }

        return response()->json(['url'=>$request->long_url],200);
    
    }


    public function index(){
        try {
            $shortlinks=Shortlink::where('user_id',Auth::user()->id)->withCount('shortlink_logs')->get();
           
            return ShortlinkResource::collection($shortlinks);
        
        } catch (\Throwable $th) {
        //throw $th;
        }
    }


    public function editShortlink(Shortlink $shortlink,Request $request)
    {
        $request->validate([
            'password'=>'min:6'
        ]);
      
        if($request->has('custom_url') && $request->custom_url != $shortlink->key && $request->use_custom_url){
            $request->validate([
                'custom_url'=>'min:5'
            ]);
            $shortlink->key=$request->custom_url;
        }
        if(!$request->use_custom_url && $request->custom_url != null){
            $createShortlink = new ShortlinkHelper();
            $key = $createShortlink->createToken($shortlink->long_url);
            $shortlink->key= $key;            
        }
        if($request->has('password') && $shortlink->password != $request->password){
            $shortlink->password=base64_encode($request->password);
        }
        if(!$request->has('password') && $shortlink->password != null){
            $shortlink->password=null;
        }
     
        $shortlink->save();
    }


    public function generateShortlink(Request $request){
        
        // dd($request->all());
        $request->validate([
            'url'=>'required'
        ]);

        $url = $request->url;
        $token='';
       if(is_null($request->custom_url)){
            $createShortlink = new ShortlinkHelper();
            while (true) {
            
                $token = $createShortlink->createToken($url);

                if(!Shortlink::where('key',$token)->first()){
                    break;
                }
            }
        }else{
            $token= $request->custom_url;
        }
        Shortlink::create([
            'key'=>$token,
            'long_url'=>$url,
            'password'=>$request->password!=null?base64_encode($request->password):null,
            'user_id'=>Auth::user()->id
        ]);
        $request->merge(['message'=>"You create $token shortlink for $url"]);
        return response()->json([
            'message'=>'link generate successfully'
        ],200);
    }

    public function removeShortlink($shortlink,Request $request){
        try {
            
            $delete = Shortlink::where('key',$shortlink)->where('user_id',Auth::user()->id)->first()->delete();
            
            if($delete == 0){
                return response()->json(['message'=>'unathorized user'],401);
            }

            return response()->json(['message'=>'delete shortlink'],200);
        } catch (\Throwable $th) {
            
        }
    }

    public function  showShortlink(Shortlink $shortlink) {
        return new ShortlinkResource($shortlink);
    }

    public function shortlinkClick(Shortlink $shortlink,Request $request)
    {

        $times =[
            'day'=>"'%Y-%m-%d'",
            'week'=>"'%Y-%m-%W'",
            'month'=>"'%Y-%m'"
        ];
        $time = $times[$request->time];

        $date = DateRangeHelper::getDateRange($request->date,$time);
        $time = str_replace('W','u',$time);
        
        $clickeds=ShortlinkLog::where('shortlink_id',$shortlink->id)->whereBetween(DB::raw("DATE_FORMAT(created_at,
        $time)"),[$date[0],$date[1]])->select(DB::raw("count(*) as click, date_format(created_at,$time) as label"))->groupBy(DB::raw("date_format(created_at,$time)"))->get();

        $diff =$request->time == 'week'? date_diff(date_create($date[2]),date_create($date[3])):date_diff(date_create($date[0]),date_create($date[1]));
        $diffFormat = $request->time == 'month'? '%m':'%a';
        $length = $request->time == 'week'?floor($diff->format($diffFormat)/7)+1:$diff->format($diffFormat)+1;


        $data = ['label'=>[],'data'=>[]];

        for($i = 0; $i<$length;$i++){
            
            array_push($data['label'],date_format(date_add(date_create($date[0]),date_interval_create_from_date_string("$i $request->time")),$request->time == 'week'?'Y-m-W':str_replace(["%","'"],'',$time)));
            $clicked = $clickeds->where('label',$data['label'][$i])->first();
            array_push($data['data'],$clicked == null? 0:$clicked->click);
        }
        return response()->json($data,200);
    }

    public function qrcode(Request $request){
        // dd($request->all());
        $request->validate([
            'shortlink'=>'required'
        ]);
        $id = explode('/',$request->shortlink);
        $shortlink = Shortlink::where('key',$id[sizeof($id)-1])->first();
        if(!$shortlink){
            return response()->json(['message'=>'not found'],400);
        }
        $options = new QROptions();
        // $options->outputType          = QROutputInterface::GDIMAGE_PNG;
        $options->returnResource = true;
       
        // $img = $id[sizeof($id)-1].".png";
        $qrcode = (new QRCode($options))->render($request->shortlink);
        
        // imagepng($qrcode,"qrcode-img/$img");
        // $shortlink->update([
        //     'qrcode'=>$img
        // ]);
        // $request->merge(['message'=>"You create qrcode for $shortlink->key shortlink"]);
        return response()->json(['qrcode'=>$qrcode],200);
    }
}
