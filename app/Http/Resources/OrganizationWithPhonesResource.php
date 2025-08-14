<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="OrganizationWithPhonesResource",
 *     title="Ресурс организации с телефонами",
 *     description="Данные организации, включая идентификатор, наименование и список телефонных номеров.",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/OrganizationResource")
 *     },
 *     @OA\Property(
 *         property="phones",
 *         title="Телефоны",
 *         description="Список телефонных номеров, связанных с организацией.",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrganizationPhoneResource")
 *     )
 * )
 */
class OrganizationWithPhonesResource extends JsonResource
{
    public function toArray($request)
    {
        $organizationData = OrganizationResource::make($this->resource)->toArray($request);

        return array_merge($organizationData, [
            'phones' => OrganizationPhoneResource::collection($this->whenLoaded('phones'))
        ]);
    }
}