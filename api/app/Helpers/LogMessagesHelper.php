<?php

namespace App\Helpers;


class LogMessagesHelper{

    public function getMessage($path,$method,$request=null){
        
        $action = explode('/',$path);
        $message = '';
        if($action[sizeof($action)-1]=='login'|| $action[sizeof($action)-1]=='logout'){
            $message = "You"." ".$action[sizeof($action)-1];
        }else{
            $method = $method == 'POST'?'create':strtolower($method);
            if($method == 'delete'){
                $message="You"." ".$method." ".$action[sizeof($action)-1]." ".$action[1];
            }
            else if($method == 'put'){
                $message = $request['message'];
            }
            else{
                if($request == null){
                    $message="You"." ".$method." ".$action[1];
                }
                else{
                    $message="You"." ".$method." $request->data ".$action[1];
                }
            }
        }
        
        return $message;
    }
}