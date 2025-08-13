<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrganizationWithBuilding",
 *     title="Organization with Building Info",
 *     description="Организация с информацией о здании",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/OrganizationWithPhones"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="building",
 *                 ref="#/components/schemas/Building"
 *             )
 *         )
 *     }
 * )
 */
class OrganizationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phones' => OrganizationPhoneResource::collection($this->phones),
            'building' => new BuildingResource($this->building)
        ];
    }
}