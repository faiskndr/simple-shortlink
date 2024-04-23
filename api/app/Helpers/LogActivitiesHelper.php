<?php

namespace App\Helpers;

class LogActivitiesHelper
{
    public static function getAction($activity)
    {
      
        $action = strtolower($activity['action']);
       
        // dd($change);
        return $action;
    }

    public static function getActivity($activity)
    {
        $old_value = json_decode($activity['old_value']);
        $new_value = json_decode($activity['new_value']);
        // dd($old_value,$new_value);
        $activities = [];
        // $except = ['created_at','updated_at'];
        if(!empty($old_value)){
            array_push($activities,['old'=>$old_value, 'new'=>$new_value]);
        }
        else{
            array_push($activities,['old'=>null,'new'=>$new_value]);
        }
        return $activities;
    }
}