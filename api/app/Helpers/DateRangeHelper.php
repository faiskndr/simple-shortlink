<?php

namespace App\Helpers;


class DateRangeHelper
{
    public static function getDateRange($date,$format='Y-m-d') {
        if(sizeof($date)<2)
        {
            if($date[0]<date_format(date_create(),str_replace(["%","'"],'',$format)))
            {
                $newDate = date_add(date_create($date[0]),date_interval_create_from_date_string('5 days'));
                array_push($date,date_format($newDate>date_create()?date_create():$newDate,str_replace(["%","'"],'',$format)));
            }
            else
            {  
                array_unshift($date,date_format(date_sub(date_create($date[0]),date_interval_create_from_date_string('5 days')),str_replace(["%","'"],'',$format)));
            }
        }
        if($date[0]>$date[1])
        {
            $tmp = $date[0];
            $date[0] =$date[1];
            $date[1] = $tmp;
        }
        $date[2] = $date[0];
        $date[3]=$date[1];
        $date[0]=date_format(date_create($date[0]),str_replace(["%","'"],'',$format));
        $date[1]=date_format(date_create($date[1]),str_replace(["%","'"],'',$format));
        return $date;
    }
}