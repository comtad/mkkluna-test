<?php

use App\Http\Controllers\GetBuildingOrganizationsController;
use App\Http\Controllers\GetOrganizationById;
use App\Http\Controllers\GetOrganizationsByActivity;
use App\Http\Controllers\GetOrganizationsByActivityTree;
use App\Http\Controllers\GetOrganizationsByName;
use App\Http\Controllers\GetOrganizationsInRadius;
use App\Http\Controllers\GetOrganizationsInRectangle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/buildings/organizations', GetBuildingOrganizationsController::class)
    ->middleware('api.key');
Route::get('/activities/organizations', GetOrganizationsByActivity::class)
    ->middleware('api.key');
Route::get('/organizations/nearby', GetOrganizationsInRadius::class)
    ->middleware('api.key');
Route::get('/organizations/in-rectangle', GetOrganizationsInRectangle::class)
    ->middleware('api.key');
Route::get('/organizations/{id}', GetOrganizationById::class)
    ->middleware('api.key');
Route::get('/organizations/by-activity-tree', GetOrganizationsByActivityTree::class)
    ->middleware('api.key');
Route::get('/organizations/by-name', GetOrganizationsByName::class)
    ->middleware('api.key');