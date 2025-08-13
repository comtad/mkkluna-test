<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Http\Resources\BuildingWithOrganizationsResource;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/buildings/organizations",
 *     summary="Получить организации в здании",
 *     description="Возвращает информацию о здании со списком всех организаций и их телефонами",
 *     operationId="getBuildingOrganizations",
 *     tags={"Buildings"},
 *     @OA\Parameter(
 *         name="api_key",
 *         in="query",
 *         required=true,
 *         description="Ключ доступа API",
 *         example="123",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="building_id",
 *         in="query",
 *         required=true,
 *         description="ID здания",
 *         example="3",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/BuildingWithOrganizations")
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
 *         description="Здание не найдено",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Building not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The building id field is required."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="building_id",
 *                     type="array",
 *                     @OA\Items(type="string", example="The building id field is required.")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class GetBuildingOrganizationsController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|integer|exists:buildings,id'
        ]);

        $building = Building::with('organizations.phones')->findOrFail($validated['building_id']);

        return new BuildingWithOrganizationsResource($building);
    }
}