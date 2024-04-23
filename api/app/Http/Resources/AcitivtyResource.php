<?php

namespace App\Http\Resources;

use App\Helpers\LogActivitiesHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcitivtyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        JsonResource::withoutWrapping();
        return[
            'id'=>$this->id,
            'username'=>$this->getUser(),
            'activity'=>LogActivitiesHelper::getAction($this->getAttributes()),
            'details'=>LogActivitiesHelper::getActivity($this->getAttributes()),
            'created_at'=>date_format($this->created_at,'Y-m-d')
        ];
    }

    private function getUser() {
        $user = $this->whenLoaded('user');

        return $user->username;
    }
}
