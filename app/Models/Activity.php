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


    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id')->with('children');
    }

    public static function getDescendantsIdsBatch(array $parentIds): array
    {
        if (empty($parentIds)) {
            return [];
        }

        $descendants = [];
        $parents = self::whereIn('id', $parentIds)->get(['id', '_lft', '_rgt']);
        foreach ($parents as $parent) {
            $descendants = array_merge($descendants, self::descendantsOf($parent->id)->pluck('id')->toArray()); // Или manual where _lft > parent._lft and _lft < parent._rgt
        }

        return array_unique($descendants);
    }


    public function allChildren()
    {
        return $this->hasMany(Activity::class, 'parent_id')->with('allChildren');
    }

    public static function getDescendantsIds($id)
    {
        return static::where('_lft', '>=', function ($query) use ($id) {
            $query->select('_lft')
                ->from('activities')
                ->where('id', $id);
        })
            ->where('_rgt', '<=', function ($query) use ($id) {
                $query->select('_rgt')
                    ->from('activities')
                    ->where('id', $id);
            })
            ->pluck('id');
    }



    public static function getDepthCache($activities)
    {
        $activityIds = $activities->pluck('id')->unique();

        return self::whereIn('id', $activityIds)
            ->pluck('depth', 'id')
            ->toArray();
    }
}