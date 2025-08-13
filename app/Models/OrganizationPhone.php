<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationPhone extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['number'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}