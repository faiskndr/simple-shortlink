<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortlinkResource extends JsonResource
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
            'key'=>$this->key,
            'long_url'=>$this->long_url,
            'password'=>$this->password,
            'clicked'=>$this->whenCounted('shortlink_logs'),
            'user_id'=>$this->user_id,
            'created_at'=>date_format($this->created_at,'Y-m-d')
        ];
    }
}
