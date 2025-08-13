<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Organization;
use App\Http\Resources\OrganizationResource;
use Illuminate\Http\Request;

class GetOrganizationsByActivityTree extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'activity_name' => 'required|string|min:3'
        ]);

        $activity = Activity::where('name', 'like', '%'.$validated['activity_name'].'%')
            ->first();

        if (!$activity) {
            return response()->json(['error' => 'Activity not found'], 404);
        }

        $activityIds = $activity->getDescendantIds();

        $organizations = Organization::whereHas('activities', function($query) use ($activityIds) {
            $query->whereIn('activities.id', $activityIds);
        })
            ->with(['phones', 'building'])
            ->get();

        return OrganizationResource::collection($organizations);
    }
}