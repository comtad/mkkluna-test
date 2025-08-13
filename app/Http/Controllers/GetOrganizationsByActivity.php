<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Http\Resources\ActivityWithOrganizationsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
/**
 * @OA\Get(
 *     path="/api/activities/organizations",
 *     summary="Получить организации по виду деятельности",
 *     description="Возвращает вид деятельности с организациями, относящимися к нему",
 *     operationId="getOrganizationsByActivity",
 *     tags={"Activities"},
 *     @OA\Parameter(
 *         name="api_key",
 *         in="query",
 *         required=true,
 *         description="Ключ доступа API",
 *         example="123",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="activity_name",
 *         in="query",
 *         required=true,
 *         description="Название вида деятельности",
 *         example="Рестораны",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/ActivityWithOrganizations")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неверный API ключ",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Invalid API key")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Вид деятельности не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Activity not found")
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
                return response()->json(['error' => 'Activity not found'], 404);
            }
        }

        return new ActivityWithOrganizationsResource($activity);
    }
}