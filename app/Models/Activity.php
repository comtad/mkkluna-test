<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;
use Exception;

class Activity extends Model
{
    use NodeTrait;

    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['name'];
    protected $with = ['children'];

    const MAX_DEPTH = 3;

    protected static function booted()
    {
        static::saving(function ($node) {
            if ($node->parent_id) {
                $parent = Activity::find($node->parent_id);

                if ($parent->depth >= self::MAX_DEPTH - 1) {
                    throw new Exception("Максимальная глубина вложенности: " . self::MAX_DEPTH . " уровня");
                }
            }
        });

        static::updating(function ($node) {
            if ($node->isDirty('parent_id')) {
                $newParent = $node->parent_id
                    ? Activity::find($node->parent_id)
                    : null;

                $newDepth = $newParent ? $newParent->depth + 1 : 0;

                if ($newDepth > self::MAX_DEPTH - 1) {
                    throw new Exception("Максимальная глубина вложенности: " . self::MAX_DEPTH . " уровня");
                }

                $maxChildDepth = $node->descendants()->withDepth()->max('depth');
                if ($maxChildDepth && ($maxChildDepth + $newDepth - $node->depth) > self::MAX_DEPTH - 1) {
                    throw new Exception("Перемещение нарушит максимальную глубину вложенности в поддереве");
                }
            }
        });
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
    }

    public function getDepthAttribute()
    {
        return $this->ancestors()->count();
    }

    public function scopeLimitedDepth($query, $maxDepth = self::MAX_DEPTH)
    {
        return $query->with(['children' => function ($q) use ($maxDepth) {
            if ($maxDepth > 1) {
                $q->limitedDepth($maxDepth - 1);
            }
        }]);
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id')->with('children');
    }

    public function getDescendantIds()
    {
        return $this->descendants()->pluck('id')->push($this->id);
    }
}