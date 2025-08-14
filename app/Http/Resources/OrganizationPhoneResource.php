<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="OrganizationPhoneResource",
 *     title="Ресурс телефонного номера организации",
 *     description="Представление контактного телефонного номера организации",
 *     type="object",
 *     required={"id", "number"},
 *     @OA\Property(
 *         property="id",
 *         title="Идентификатор",
 *         description="Уникальный числовой идентификатор телефонного номера",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="number",
 *         title="Номер телефона",
 *         description="Контактный телефонный номер в международном или национальном формате",
 *         type="string",
 *         example="+7 (495) 123-45-67"
 *     )
 * )
 */
class OrganizationPhoneResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
        ];
    }
}