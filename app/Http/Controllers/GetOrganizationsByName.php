<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Resources\OrganizationResource;
use Illuminate\Http\Request;

class GetOrganizationsByName extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3'
        ]);

        $organizations = Organization::where('name', 'like', '%'.$validated['name'].'%')
            ->with(['phones', 'building'])
            ->get();

        return OrganizationResource::collection($organizations);
    }
}