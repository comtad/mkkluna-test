<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Resources\OrganizationResource;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/organizations/by-name",
 *     summary="Поиск организаций по названию",
 *     description="Находит организации, название которых содержит указанную строку (минимум 3 символа). Возвращает список организаций с телефонами и данными здания. Если ничего не найдено, возвращается пустой список.",
 *     operationId="getOrganizationsByName",
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
 *         name="name",
 *         in="query",
 *         description="Часть названия организации (минимум 3 символа).",
 *         required=true,
 *         @OA\Schema(type="string", minLength=3),
 *         example="Рем"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успех. Список организаций (может быть пустым)",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/OrganizationResource")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Название должно содержать минимум 3 символа."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="name",
 *                     type="array",
 *                     @OA\Items(type="string", example="Название должно содержать минимум 3 символа.")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class GetOrganizationsByName extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3'
        ]);

        $organizations = Organization::where('name', 'ILIKE', '%'.$validated['name'].'%')
        ->with(['phones', 'building'])
            ->get();


        if ($organizations->isEmpty()) {
             return response()->json(['error' => 'Организации не найдены'], 404);
        }

        return OrganizationResource::collection($organizations);
    }
}