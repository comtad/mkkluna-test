<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrganizationPhone",
 *     title="Organization Phone",
 *     description="Телефон организации",
 *     @OA\Property(property="id", type="integer", example=12152),
 *     @OA\Property(property="number", type="string", example="(35222) 72-6391")
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