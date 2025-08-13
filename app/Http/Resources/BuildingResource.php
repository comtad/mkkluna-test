<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Building",
 *     title="Building Info",
 *     description="Информация о здании",
 *     @OA\Property(property="id", type="integer", example=3),
 *     @OA\Property(property="address", type="string", example="г. Москва, Очаковское шоссе, дом 14, строение 8"),
 *     @OA\Property(
 *         property="coordinates",
 *         type="object",
 *         @OA\Property(property="lat", type="number", format="float", example=55.6938523),
 *         @OA\Property(property="lng", type="number", format="float", example=37.4527517)
 *     )
 * )
 */
class BuildingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'coordinates' => $this->getCoordinates(),
        ];
    }

    protected function getCoordinates()
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}