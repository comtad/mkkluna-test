<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Http\Resources\OrganizationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
/**
 * @OA\Get(
 *     path="/api/organizations/in-rectangle",
 *     summary="Поиск организаций в прямоугольной области",
 *     description="Возвращает организации, находящиеся в пределах прямоугольной области, заданной юго-западной и северо-восточной точками",
 *     operationId="getOrganizationsInRectangle",
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
 *         name="sw_lat",
 *         in="query",
 *         required=true,
 *         description="Широта юго-западного угла",
 *         example="55.6688523",
 *         @OA\Schema(type="number", format="float")
 *     ),
 *     @OA\Parameter(
 *         name="sw_lng",
 *         in="query",
 *         required=true,
 *         description="Долгота юго-западного угла",
 *         example="37.4277517",
 *         @OA\Schema(type="number", format="float")
 *     ),
 *     @OA\Parameter(
 *         name="ne_lat",
 *         in="query",
 *         required=true,
 *         description="Широта северо-восточного угла",
 *         example="55.7188523",
 *         @OA\Schema(type="number", format="float")
 *     ),
 *     @OA\Parameter(
 *         name="ne_lng",
 *         in="query",
 *         required=true,
 *         description="Долгота северо-восточного угла",
 *         example="37.4777517",
 *         @OA\Schema(type="number", format="float")
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
 *             @OA\Property(property="message", type="string", example="The sw_lat field is required.")
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

        return OrganizationResource::collection($organizations);
    }
}