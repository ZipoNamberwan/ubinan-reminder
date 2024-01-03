<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SentMessagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        return [
            'receiver' => $request->receiver,
            'type' => $request->type,
            'message' => $request->message,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'ids' => $request->ids
        ];
    }
}
