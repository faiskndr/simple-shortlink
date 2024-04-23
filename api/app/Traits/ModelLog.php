<?php

namespace App\Traits;

use App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;

trait ModelLog
{
    public static function bootModelLog()
    {
      

        static::created(function($model)
        {
            self::storeLog($model,'CREATED');
        });

        static::updating(function($model)
        {
            self::storeLog($model,'UPDATED');
        });

        static::deleting(function($model)
        {
            self::storeLog($model,'DELETED');
        });
    }

    private static function storeLog($model,$action) 
    {
        // dd($model);
        $new_value = $model->getAttributes();
        
        $table = preg_filter('/_+/',' ',$model->getTable());
        if($table == null){
            $table = $model->getTable();
        }
        
        if($action == 'CREATED' && $table == 'users'){
            $action = 'REGISTERED';
        }

        $log = new LogActivity();
        $log->user_id =Auth::user()==null?$model->id: Auth::user()->id;
        $log->action = $action." ".$table;
        $log->old_value = $action != 'UPDATED'?null:json_encode($model->getOriginal());
        $log->new_value = $new_value != null? json_encode($new_value) : null;
        $log->save();
    }
}
