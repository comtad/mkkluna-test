<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Http\Resources\OrganizationWithPhonesResource;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/organizations/nearby",
 *     summary="Поиск организаций в заданном радиусе",
 *     description="Находит организации, расположенные в указанном радиусе от заданной географической точки. Возвращает список организаций с телефонными номерами.",
 *     operationId="getOrganizationsInRadius",
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
 *         name="lat",
 *         in="query",
 *         description="Широта центральной точки (от -90 до 90).",
 *         required=true,
 *         @OA\Schema(type="number", minimum=-90, maximum=90),
 *         example=55.7069
 *     ),
 *     @OA\Parameter(
 *         name="lng",
 *         in="query",
 *         description="Долгота центральной точки (от -180 до 180).",
 *         required=true,
 *         @OA\Schema(type="number", minimum=-180, maximum=180),
 *         example=37.5422026
 *     ),
 *     @OA\Parameter(
 *         name="radius",
 *         in="query",
 *         description="Радиус поиска в метрах (минимум 0).",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=0),
 *         example=1000
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успех. Список организаций",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/OrganizationWithPhonesResource")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Не найдено",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Организации не найдены")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The lat field is required."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="lat",
 *                     type="array",
 *                     @OA\Items(type="string", example="The lat field is required.")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class GetOrganizationsInRadius extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:0',
        ]);

        $center = new Point(
            $validated['lat'],
            $validated['lng'],
            4326
        );

        $buildings = Building::with('organizations.phones')
            ->whereDistanceSphere('coordinates', $center, '<=', $validated['radius'])
            ->get();

        $organizations = $buildings->flatMap->organizations;

        if ($organizations->isEmpty()) {
            return response()->json(['error' => 'Организации не найдены'], 404);
        }

        return OrganizationWithPhonesResource::collection($organizations);
    }
}