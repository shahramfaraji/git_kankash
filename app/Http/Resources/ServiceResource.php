<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            "id"=> $this->id,
            "title"=> $this->user->user. "(" .  $this->city->name . ")(". count($this->along).")",
            "date"=> $this->date,
            "backgroundColor"=> ($this->price == "0" || $this->price != null) ? "green" : "red",
        ];
    }

}