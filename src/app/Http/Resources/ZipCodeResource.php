<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ZipCodeResource extends JsonResource
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
            'zip_code' => $this->resource->zip_code,
            'locality' => $this->resource->locality,
            'federal_entity' => [
                'key' => $this->resource->municipality->federalEntity->key,
                'name' => $this->resource->municipality->federalEntity->name,
                'code' => $this->resource->municipality->federalEntity->code,
            ],
            'settlements' => $this->resource->settlements->map(fn ($settlement) => [
                'key' => $settlement->key,
                'name' => $settlement->name,
                'zone_type' => $settlement->zone_type,
                'settlement_type' => [
                    'name' => $settlement->settlementType->name,
                ],
            ]),
            'municipality' => [
                'key' => $this->resource->municipality->key,
                'name' => $this->resource->municipality->name,
            ],
        ];
    }
}
