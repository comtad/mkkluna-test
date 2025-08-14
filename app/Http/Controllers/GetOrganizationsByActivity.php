<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Http\Resources\ActivityWithOrganizationsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @OA\Get(
 *     path="/api/activities/organizations",
 *     summary="Получение списка организаций по виду деятельности",
 *     description="Находит вид деятельности по названию и возвращает связанные организации с телефонными номерами. Сначала выполняется точный поиск, при отсутствии результатов — частичный поиск с сортировкой по длине названия.",
 *     operationId="getOrganizationsByActivity",
 *     tags={"Виды деятельности"},
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
 *         description="Название вида деятельности (минимум 3 символа).",
 *         required=true,
 *         @OA\Schema(type="string", minLength=3),
 *         example="Рестораны"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успех. Данные вида деятельности с организациями",
 *         @OA\JsonContent(ref="#/components/schemas/ActivityWithOrganizationsResource")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Не найдено",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Вид деятельности не найден")
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
class GetOrganizationsByActivity extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'activity_name' => 'required|string|min:3'
        ]);

        $searchTerm = Str::lower($validated['activity_name']);

        $activity = Activity::whereRaw('LOWER(name) = ?', [$searchTerm])
            ->with(['organizations.phones'])
            ->first();

        if (!$activity) {
            $activity = Activity::whereRaw('LOWER(name) LIKE ?', ['%'.$searchTerm.'%'])
                ->with(['organizations.phones'])
                ->orderByRaw('LENGTH(name)')
                ->first();

            if (!$activity) {
                return response()->json(['error' => 'Вид деятельности не найден'], 404);
            }
        }

        return new ActivityWithOrganizationsResource($activity);
    }
}