<?php

use App\Http\Controllers\{
    GetBuildingOrganizationsController,
    GetOrganizationById,
    GetOrganizationsByActivity,
    GetOrganizationsByName,
    GetOrganizationsInRadius,
    GetOrganizationsInRectangle,
    GetOrgsByActivityTreeController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('organizations')->middleware('api.key')->group(function () {
    Route::get('activity-tree', GetOrgsByActivityTreeController::class);
    Route::get('by-name', GetOrganizationsByName::class);
    Route::get('nearby', GetOrganizationsInRadius::class);
    Route::get('in-rectangle', GetOrganizationsInRectangle::class);
    Route::get('by-id', GetOrganizationById::class);
});

Route::prefix('buildings')->middleware('api.key')->group(function () {
    Route::get('organizations', GetBuildingOrganizationsController::class);
});

Route::prefix('activities')->middleware('api.key')->group(function () {
    Route::get('organizations', GetOrganizationsByActivity::class);
});

// Системные маршруты
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');