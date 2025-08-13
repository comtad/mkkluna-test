<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ActivityTree",
 *     title="Activity Tree",
 *     description="Дерево видов деятельности с вложенными элементами",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Рестораны"),
 *     @OA\Property(property="depth", type="integer", example=0),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         description="Дочерние элементы дерева",
 *         @OA\Items(ref="#/components/schemas/ActivityTree")
 *     )
 * )
 */
class ActivityTreeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'depth' => $this->depth,
            'children' => $this->when(
                $this->children->isNotEmpty(),
                self::collection($this->children)
            )
        ];
    }
}