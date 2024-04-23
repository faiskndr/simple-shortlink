<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortlinkLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function shortlink() 
    {
        return $this->belongsTo(Shortlink::class,'shortlink_id','id');
    }
}
