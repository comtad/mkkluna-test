<?php

namespace App\Http\Resources;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ActivityWithOrganizationsResource",
 *     title="Ресурс вида деятельности с организациями",
 *     description="Представление вида экономической деятельности с привязанными организациями",
 *     type="object",
 *     @OA\Property(
 *         property="activity",
 *         title="Данные вида деятельности",
 *         description="Структура, содержащая основные параметры вида деятельности и связанные организации",
 *         type="object",
 *         allOf={
 *             @OA\Schema(ref="#/components/schemas/ActivityResource"),
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="organizations",
 *                     title="Связанные организации",
 *                     description="Перечень организаций, осуществляющих данный вид экономической деятельности",
 *                     type="array",
 *                     @OA\Items(ref="#/components/schemas/OrganizationWithPhonesResource")
 *                 )
 *             )
 *         }
 *     )
 * )
 */
class ActivityWithOrganizationsResource extends ActivityResource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);
        unset($data['children']);

        $data['organizations'] = OrganizationWithPhonesResource::collection($this->organizations);

        return [
            'activity' => $data
        ];
    }
}