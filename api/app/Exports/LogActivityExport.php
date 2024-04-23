<?php

namespace App\Exports;

use App\Http\Resources\AcitivtyResource;
use App\Models\LogActivity;
use Maatwebsite\Excel\Concerns\FromCollection;

class LogActivityExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $log_activities = LogActivity::with('user')->orderBy('created_at','desc')->get();
        return AcitivtyResource::collection($log_activities);
    }
}
