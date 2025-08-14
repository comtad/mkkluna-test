<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ActivityResource",
 *     title="Ресурс вида деятельности",
 *     description="Структура данных, представляющая вид экономической деятельности с иерархической организацией",
 *     type="object",
 *     required={"id", "name", "depth"},
 *     @OA\Property(
 *         property="id",
 *         title="Идентификатор",
 *         description="Уникальный числовой идентификатор вида деятельности",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         title="Наименование",
 *         description="Официальное название вида экономической деятельности",
 *         type="string",
 *         example="Производство промышленного оборудования"
 *     ),
 *     @OA\Property(
 *         property="depth",
 *         title="Уровень вложенности",
 *         description="Порядковый уровень в иерархической структуре видов деятельности (0 - корневой уровень)",
 *         type="integer",
 *         example=0
 *     ),
 *     @OA\Property(
 *         property="children",
 *         title="Дочерние элементы",
 *         description="Массив подчиненных видов деятельности в иерархической структуре (рекурсивная структура)",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ActivityResource")
 *     )
 * )
 */
class ActivityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'depth' => $this->depth,
            'children' => $this->when(
                $this->relationLoaded('children') && $this->children->isNotEmpty(),
                function () {
                    return ActivityResource::collection($this->children);
                }
            ),
        ];
    }
}