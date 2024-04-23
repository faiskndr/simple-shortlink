<?php

namespace Database\Seeders;

use App\Models\ShortlinkLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShortlinkLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $date = date_create('2024-02-01');
        for($i=0; $i<10; $i++){
            $num = rand(5,50);
            date_add($date,date_interval_create_from_date_string("$i day"));
            for($j=0; $j<$num;$j++){
                ShortlinkLog::create([
                    'shortlink_id'=>3,
                    'clicked'=>1,
                    'created_at'=>$date,
                    'updated_at'=>$date
                ]);
            }
        }
    }
}
