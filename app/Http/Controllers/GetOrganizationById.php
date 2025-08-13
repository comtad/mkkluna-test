<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Resources\FullOrganizationResource;
use Illuminate\Http\Request;

class GetOrganizationById extends Controller
{
    public function __invoke(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        $validated = $request->validate([
            'id' => 'required|integer|exists:organizations,id'
        ]);

        $organization = Organization::with([
            'phones',
            'building',
            'activities' => function ($query) {
                $query->with('children')->whereNull('parent_id');
            }
        ])
            ->find($validated['id']);

        return new FullOrganizationResource($organization);
    }
}