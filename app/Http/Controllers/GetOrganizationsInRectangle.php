<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizationWithPhonesResource;
use App\Models\Building;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/organizations/in-rectangle",
 *     summary="Поиск организаций в прямоугольной области",
 *     description="Находит организации, расположенные в указанной прямоугольной области на карте (bounding box). Возвращает список организаций с телефонными номерами.",
 *     operationId="getOrganizationsInRectangle",
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
 *         name="sw_lat",
 *         in="query",
 *         description="Широта юго-западного угла области (от -90 до 90).",
 *         required=true,
 *         @OA\Schema(type="number", minimum=-90, maximum=90),
 *         example=55.7
 *     ),
 *     @OA\Parameter(
 *         name="sw_lng",
 *         in="query",
 *         description="Долгота юго-западного угла области (от -180 до 180).",
 *         required=true,
 *         @OA\Schema(type="number", minimum=-180, maximum=180),
 *         example=37.5
 *     ),
 *     @OA\Parameter(
 *         name="ne_lat",
 *         in="query",
 *         description="Широта северо-восточного угла области (от -90 до 90).",
 *         required=true,
 *         @OA\Schema(type="number", minimum=-90, maximum=90),
 *         example=55.8
 *     ),
 *     @OA\Parameter(
 *         name="ne_lng",
 *         in="query",
 *         description="Долгота северо-восточного угла области (от -180 до 180).",
 *         required=true,
 *         @OA\Schema(type="number", minimum=-180, maximum=180),
 *         example=37.6
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
 *             @OA\Property(property="message", type="string", example="The sw lat field is required."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="sw_lat",
 *                     type="array",
 *                     @OA\Items(type="string", example="The sw lat field is required.")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class GetOrganizationsInRectangle extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'sw_lat' => 'required|numeric|between:-90,90',
            'sw_lng' => 'required|numeric|between:-180,180',
            'ne_lat' => 'required|numeric|between:-90,90',
            'ne_lng' => 'required|numeric|between:-180,180',
        ]);

        $buildings = Building::with('organizations.phones')
            ->whereRaw('ST_Within(coordinates, ST_MakeEnvelope(?, ?, ?, ?, 4326))', [
                $validated['sw_lng'],
                $validated['sw_lat'],
                $validated['ne_lng'],
                $validated['ne_lat']
            ])
            ->get();

        $organizations = $buildings->flatMap->organizations;

        if ($organizations->isEmpty()) {
            return response()->json(['error' => 'Организации не найдены'], 404);
        }

        return OrganizationWithPhonesResource::collection($organizations);
    }
}