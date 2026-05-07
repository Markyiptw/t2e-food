<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['restaurant_id', 'data'])]
class Hour extends Model
{
    public function periods(): HasMany
    {
        return $this->hasMany(Period::class);
    }
}
