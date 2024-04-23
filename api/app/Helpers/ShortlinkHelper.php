<?php

namespace App\Helpers;

class ShortlinkHelper{


   public function createToken($link){
        $arr_link = explode('/',$link);
        $uniquelink = $link;
        if(sizeof($arr_link)>1){
            $domain = explode('.',$arr_link[2]);
            $randstr = str_split($link);
            $uniquelink = $domain[1].$randstr[rand(0,sizeof($randstr)-1)].$arr_link[sizeof($arr_link)-1].time();
        }
        
        $dec = '';

        $arr = str_split($uniquelink);
    
        for($i = 1; $i < sizeof($arr); $i++){
            $tmp = ord($arr[$i-1]) + ord($arr[$i]);
            if($tmp > 127){
                $tmp -= 127;
            }

            $dec .= $tmp;
            
        }
        
        $nums = str_split($dec,10);
        $result = 0;
        foreach ($nums as $num) {
            $result += (int)$num;
        }

        $shortlink = $this->base62($result);
    
        return $shortlink;
    }


    private function base62($num){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $short_url =[];
        
        while($num >0){
            $char = $chars[$num%62];
            array_push($short_url,$char);
            $num = floor($num/62);
    
        }
        $result = join("",array_reverse($short_url));
        return $result;
    }
}