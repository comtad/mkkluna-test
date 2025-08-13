<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Http\Resources\OrganizationResource;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Http\Request;
/**
 * @OA\Get(
 *     path="/api/organizations/nearby",
 *     summary="Поиск организаций в радиусе от точки",
 *     description="Возвращает организации, находящиеся в указанном радиусе (в метрах) от заданной точки",
 *     operationId="getOrganizationsInRadius",
 *     tags={"Geosearch"},
 *     @OA\Parameter(
 *         name="api_key",
 *         in="query",
 *         required=true,
 *         description="Ключ доступа API",
 *         example="123",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="lat",
 *         in="query",
 *         required=true,
 *         description="Широта центра поиска",
 *         example="55.6938523",
 *         @OA\Schema(type="number", format="float")
 *     ),
 *     @OA\Parameter(
 *         name="lng",
 *         in="query",
 *         required=true,
 *         description="Долгота центра поиска",
 *         example="37.4527517",
 *         @OA\Schema(type="number", format="float")
 *     ),
 *     @OA\Parameter(
 *         name="radius",
 *         in="query",
 *         required=true,
 *         description="Радиус поиска в метрах",
 *         example="5000",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/OrganizationWithBuilding")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The lat field is required.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неверный API ключ",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Invalid API key")
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

        return OrganizationResource::collection($organizations);
    }
}