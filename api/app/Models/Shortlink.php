<?php

namespace App\Models;

use App\Traits\ModelLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shortlink extends Model
{
    use HasFactory,ModelLog;

    protected $guarded = ['id'];


    public function shortlink_logs() 
    {
        return $this->hasMany(ShortlinkLog::class,'shortlink_id','id');
    }

    public function microsites()
    {
        return $this->hasMany(Microsite::class,'shortlink_id','id');
    }


    public function hasToken($token){
        if($this->password!=null&&empty($token)){
           return false;
        }
        return true;
    }

    public function validateToken($token){
        if($this->password != $token){
           return false;
        }

        return true;
    }
}

