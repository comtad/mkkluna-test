<?php

namespace App\Models;

use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasSpatial;

    protected $fillable = ['address', 'coordinates'];

    protected $spatialFields = [
        'coordinates' => Point::class,
    ];

    public function setCoordinatesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['coordinates'] = new Point($value['lat'], $value['lng']);
        } elseif ($value instanceof Point) {
            $this->attributes['coordinates'] = $value;
        }
    }

    public function getCoordinatesAttribute($value)
    {
        if ($value instanceof Point) {
            return $value;
        }

        if (is_string($value)) {
            return Point::fromWkt($value);
        }

        return $value;
    }

    public function getLatAttribute(): ?float
    {
        return $this->coordinates instanceof Point
            ? $this->coordinates->latitude
            : null;
    }

    public function getLngAttribute(): ?float
    {
        return $this->coordinates instanceof Point
            ? $this->coordinates->longitude
            : null;
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }
}