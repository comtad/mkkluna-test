<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrganizationWithPhones",
 *     title="Organization with Phones",
 *     description="Организация с телефонами",
 *     @OA\Property(property="id", type="integer", example=6054),
 *     @OA\Property(property="name", type="string", example="ОАО ЛифтТехПивСбыт"),
 *     @OA\Property(
 *         property="phones",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrganizationPhone")
 *     )
 * )
 */
class OrganizationWithPhonesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phones' => OrganizationPhoneResource::collection($this->phones),
        ];
    }
}