<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Http\Resources\BuildingWithOrganizationsResource;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/buildings/organizations",
 *     summary="Получение списка организаций в здании",
 *     description="Возвращает данные здания, включая связанные организации и их телефонные номера.",
 *     operationId="getBuildingOrganizations",
 *     tags={"Здания"},
 *     @OA\Parameter(
 *         name="api_key",
 *         in="query",
 *         description="Ключ API для аутентификации.",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         example="123"
 *     ),
 *     @OA\Parameter(
 *         name="building_id",
 *         in="query",
 *         description="Идентификатор здания.",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         example=2
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успех. Данные здания с организациями",
 *         @OA\JsonContent(ref="#/components/schemas/BuildingWithOrganizationsResource")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Не найдено",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Здание не найдено")
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
            'building_id' => 'required|integer'
        ]);

        $building = Building::with('organizations.phones')->find($validated['building_id']);

        if (!$building) {
            return response()->json(['error' => 'Здание не найдено'], 404);
        }

        return new BuildingWithOrganizationsResource($building);
    }
}