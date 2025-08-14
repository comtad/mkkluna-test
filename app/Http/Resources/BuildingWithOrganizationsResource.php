<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="BuildingWithOrganizationsResource",
 *     title="Ресурс здания с организациями",
 *     description="Структура данных, представляющая информацию о здании с привязанными организациями",
 *     type="object",
 *     @OA\Property(
 *         property="building",
 *         title="Данные здания",
 *         description="Объект, содержащий информацию о здании и связанных с ним организациях",
 *         type="object",
 *         allOf={
 *             @OA\Schema(ref="#/components/schemas/BuildingResource"),
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="organizations",
 *                     title="Организации в здании",
 *                     description="Список организаций, размещенных в данном здании",
 *                     type="array",
 *                     @OA\Items(ref="#/components/schemas/OrganizationWithPhonesResource")
 *                 )
 *             )
 *         }
 *     )
 * )
 */
class BuildingWithOrganizationsResource extends JsonResource
{
    public function toArray($request)
    {
        $buildingData = BuildingResource::make($this->resource)->toArray($request);
        $buildingData['organizations'] = OrganizationWithPhonesResource::collection($this->organizations);

        return [
            'building' => $buildingData
        ];
    }
}