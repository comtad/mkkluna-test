<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="FullOrganizationResource",
 *     title="Полный ресурс организации",
 *     description="Комплексное представление данных об организации",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         title="Контейнер данных",
 *         description="Объект-контейнер для данных организации",
 *         type="object",
 *         @OA\Property(
 *             property="organization",
 *             title="Данные организации",
 *             description="Объект, содержащий полную информацию об организации",
 *             type="object",
 *             @OA\Property(
 *                 property="id",
 *                 title="Идентификатор",
 *                 type="integer",
 *                 example=3022
 *             ),
 *             @OA\Property(
 *                 property="name",
 *                 title="Наименование",
 *                 type="string",
 *                 example="ПАО Cиб"
 *             ),
 *             @OA\Property(
 *                 property="phones",
 *                 title="Контактные телефоны",
 *                 description="Список контактных номеров телефонов организации",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/OrganizationPhoneResource")
 *             ),
 *             @OA\Property(
 *                 property="building",
 *                 title="Здание размещения",
 *                 description="Информация о здании, где расположена организация",
 *                 ref="#/components/schemas/BuildingResource"
 *             ),
 *             @OA\Property(
 *                 property="activities",
 *                 title="Виды деятельности",
 *                 description="Иерархическое дерево видов экономической деятельности организации",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/ActivityResource")
 *             )
 *         )
 *     )
 * )
 */
class FullOrganizationResource extends JsonResource
{
    public function toArray($request)
    {
        $organization = OrganizationResource::make($this->resource)->toArray($request);

        $organization['phones'] = OrganizationPhoneResource::collection($this->phones);
        $organization['building'] = BuildingResource::make($this->building);
        $organization['activities'] = ActivityResource::collection($this->activities);

        return [
            'organization' => $organization
        ];
    }
}