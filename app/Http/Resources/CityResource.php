<?php

namespace App\Http\Resources;

use App\Models\City;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $city = City::find($this->city_id);
        return [
            "id"=> $city->id,
            "name"=> $city->name,
            "price"=> $city->price,
            "KM"=> $city->KM
        ];
    }
}