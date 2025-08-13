<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ActivityWithOrganizations",
 *     title="Activity with Organizations",
 *     description="Вид деятельности с организациями",
 *     @OA\Property(property="id", type="integer", example=5),
 *     @OA\Property(property="name", type="string", example="Рестораны"),
 *     @OA\Property(property="depth", type="integer", example=2),
 *     @OA\Property(
 *         property="organizations",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrganizationWithBuilding")
 *     )
 * )
 */
class ActivityWithOrganizationsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'depth' => $this->depth,
            'organizations' => OrganizationResource::collection($this->organizations)
        ];
    }
}