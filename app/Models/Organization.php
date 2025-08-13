<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{

    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['name', 'building_id'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(OrganizationPhone::class);
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class)
            ->with('parent.parent');
    }
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeByActivity($query, $activityId)
    {
        return $query->whereHas('activities', function ($q) use ($activityId) {
            $q->where('id', $activityId);
        });
    }


}