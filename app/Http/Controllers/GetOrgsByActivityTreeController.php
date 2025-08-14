<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Organization;
use App\Http\Resources\FullOrganizationResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @OA\Get(
 *     path="/api/organizations/activity-tree",
 *     summary="Поиск организаций по иерархии видов деятельности",
 *     description="Находит организации, связанные с видом деятельности и всеми его потомками. Возвращает организации с телефонами, зданиями и деревом видов деятельности.",
 *     operationId="getOrgsByActivityTree",
 *     tags={"Организации"},
 *     @OA\Parameter(
 *         name="api_key",
 *         in="query",
 *         description="Ключ API для аутентификации.",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         example="123"
 *     ),
 *     @OA\Parameter(
 *         name="activity_name",
 *         in="query",
 *         description="Название вида деятельности (минимум 3 символа). Например: 'вет' для ветеринарии.",
 *         required=true,
 *         @OA\Schema(type="string", minLength=3, maxLength=255),
 *         example="вет"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успех. Организации с деревом видов деятельности",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/FullOrganizationResource")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Не найдено",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Виды деятельности не найдены")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The activity name must be at least 3 characters."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="activity_name",
 *                     type="array",
 *                     @OA\Items(type="string", example="The activity name must be at least 3 characters.")
 *                 )
 *             )
 *         )
 *     )
 * )
 */

class GetOrgsByActivityTreeController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate(['activity_name' => 'required|string|min:3|max:255']);
        $searchTerm = trim(Str::lower($validated['activity_name']));

        $rootActivities = Activity::where('name', 'ILIKE', "%{$searchTerm}%")
            ->select('id', '_lft', '_rgt')
            ->get();

        if ($rootActivities->isEmpty()) {
            return response()->json(['error' => 'Виды деятельности не найдены'], 404);
        }

        $activityIds = Activity::where(function ($q) use ($rootActivities) {
            foreach ($rootActivities as $activity) {
                $q->orWhereBetween('_lft', [$activity->_lft, $activity->_rgt]);
            }
        })->pluck('id');

        $allActivities = Activity::with('parent')
            ->whereIn('id', $activityIds)
            ->orWhereHas('descendants', function($q) use ($activityIds) {
                $q->whereIn('id', $activityIds);
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

        $organizations = Organization::whereHas('activities', fn($q) => $q->whereIn('id', $activityIds))
            ->with([
                'phones',
                'building' => fn($q) => $q->select('id', 'address', 'coordinates'),
                'activities' => fn($q) => $q->whereIn('id', $activityIds)
            ])
            ->get();

        foreach ($organizations as $org) {
            $org->setRelation('activities', $activitiesTree);
        }

        return FullOrganizationResource::collection($organizations);
    }
}