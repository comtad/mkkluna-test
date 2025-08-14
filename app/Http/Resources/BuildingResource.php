<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="BuildingResource",
 *     title="Ресурс здания",
 *     description="Структура данных, представляющая информацию о здании",
 *     type="object",
 *     required={"id", "address", "coordinates"},
 *     @OA\Property(
 *         property="id",
 *         title="Идентификатор здания",
 *         description="Уникальный числовой идентификатор объекта недвижимости",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="address",
 *         title="Адрес",
 *         description="Физический адрес местонахождения здания",
 *         type="string",
 *         example="ул. Тверская, 15"
 *     ),
 *     @OA\Property(
 *         property="coordinates",
 *         title="Географические координаты",
 *         description="Геопозиция здания в системе координат WGS 84",
 *         type="object",
 *         required={"lat", "lng"},
 *         @OA\Property(
 *             property="lat",
 *             title="Широта",
 *             description="Географическая широта в десятичных градусах",
 *             type="number",
 *             format="float",
 *             example=55.7558
 *         ),
 *         @OA\Property(
 *             property="lng",
 *             title="Долгота",
 *             description="Географическая долгота в десятичных градусах",
 *             type="number",
 *             format="float",
 *             example=37.6173
 *         )
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
            'coordinates' => [
                'lat' => $this->lat,
                'lng' => $this->lng,
            ],
        ];
    }
}