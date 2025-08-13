<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Твой API Справочник Организаций",
 *     description="API для работы с организациями, зданиями и видами деятельности",
 *     @OA\Contact(
 *         email="твой@email.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Локальный сервер"
 * )
 *
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="query",
 *     name="api_key",
 *     securityScheme="api_key"
 * )
 */
class SwaggerController extends Controller
{

}