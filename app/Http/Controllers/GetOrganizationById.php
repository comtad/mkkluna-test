<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Resources\FullOrganizationResource;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/organizations/by-id",
 *     summary="Получение информации об организации по идентификатору",
 *     description="Возвращает детальные данные организации, включая телефоны, здание и дерево видов деятельности.",
 *     operationId="getOrganizationById",
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
 *         name="id",
 *         in="query",
 *         description="Идентификатор организации.",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         example=7
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успех. Детальные данные организации",
 *         @OA\JsonContent(ref="#/components/schemas/FullOrganizationResource")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Не найдено",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Организация не найдена")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The id field is required."),
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
            'activities' => function ($query) {
                $query->with('children')->whereNull('parent_id');
            }
        ])
            ->find($validated['id']);

        if (!$organization) {
            return response()->json(['error' => 'Организация не найдена'], 404);
        }

        return new FullOrganizationResource($organization);
    }
}