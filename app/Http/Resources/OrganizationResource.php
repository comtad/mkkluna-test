<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="OrganizationResource",
 *     title="Базовый ресурс организации",
 *     description="Минимальный набор данных для идентификации организации",
 *     type="object",
 *     required={"id", "name"},
 *     @OA\Property(
 *         property="id",
 *         title="Идентификатор организации",
 *         description="Уникальный числовой идентификатор организации в системе",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         title="Наименование",
 *         description="Официальное название организации",
 *         type="string",
 *         example="ООО 'Промышленные технологии'"
 *     )
 * )
 */
class OrganizationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}