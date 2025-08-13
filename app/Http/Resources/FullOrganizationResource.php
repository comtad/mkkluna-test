<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="FullOrganization",
 *     title="Full Organization Info",
 *     description="Полная информация об организации с отношениями",
 *     @OA\Property(property="id", type="integer", example=6054),
 *     @OA\Property(property="name", type="string", example="ООО Рога и Копыта"),
 *     @OA\Property(
 *         property="phones",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrganizationPhone")
 *     ),
 *     @OA\Property(property="building", ref="#/components/schemas/Building"),
 *     @OA\Property(
 *         property="activities",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ActivityTree")
 *     )
 * )
 */
class FullOrganizationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phones' => OrganizationPhoneResource::collection($this->phones),
            'building' => $this->building ? new BuildingResource($this->building) : null,
            'activities' => ActivityTreeResource::collection($this->activities->whereNull('parent_id'))
        ];
    }
}