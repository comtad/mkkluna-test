<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Activity;
use Illuminate\Support\Collection;
use App\Http\Resources\FullOrganizationResource;
use Illuminate\Http\Request;
/**
 * @OA\Get(
 *     path="/api/organizations/by-id",
 *     summary="Получение полной информации об организации по ID",
 *     description="Возвращает детализированные данные организации, включая контактные телефоны, информацию о здании и иерархическое дерево видов деятельности",
 *     operationId="getOrganizationById",
 *     tags={"Организации"},
 *     @OA\Parameter(
 *         name="api_key",
 *         in="query",
 *         description="API-ключ для аутентификации",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         example="123"
 *     ),
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         description="Уникальный идентификатор организации",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         example=3022
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный запрос",
 *         @OA\JsonContent(ref="#/components/schemas/FullOrganizationResource")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Организация не найдена",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Организация не найдена")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="id",
 *                     type="array",
 *                     @OA\Items(type="string", example="The id field is required.")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class GetOrganizationById extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer'
        ]);

        $organization = Organization::with([
            'phones',
            'building',
            'activities:id,name,parent_id,_lft,_rgt,depth'
        ])->find($validated['id']);

        if (!$organization) {
            return response()->json(['error' => 'Организация не найдена'], 404);
        }

        if ($organization->activities->isEmpty()) {
            return new FullOrganizationResource($organization);
        }

        $activityIds = $organization->activities->pluck('id');

        $allActivities = Activity::where(function ($q) use ($activityIds) {
            $q->whereIn('id', $activityIds)
                ->orWhereHas('descendants', fn($q) => $q->whereIn('id', $activityIds));
        })
            ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'depth')
            ->orderBy('_lft')
            ->get()
            ->keyBy('id');

        $activitiesTree = new Collection;
        foreach ($allActivities as $activity) {
            if ($activity->parent_id && isset($allActivities[$activity->parent_id])) {
                $allActivities[$activity->parent_id]->children[] = $activity;
            } else {
                $activitiesTree->add($activity);
            }
        }

        $organization->setRelation('activities', $activitiesTree);

        return new FullOrganizationResource($organization);
    }
}