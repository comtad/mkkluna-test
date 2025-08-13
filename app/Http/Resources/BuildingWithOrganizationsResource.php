<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="BuildingWithOrganizations",
 *     title="Building with Organizations",
 *     description="Информация о здании с организациями",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Building"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="organizations",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/OrganizationWithPhones")
 *             )
 *         )
 *     }
 * )
 */
class BuildingWithOrganizationsResource extends BuildingResource
{
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'organizations' => $this->getOrganizations()
        ]);
    }

    protected function getOrganizations()
    {
        return OrganizationWithPhonesResource::collection($this->organizations);
    }
}