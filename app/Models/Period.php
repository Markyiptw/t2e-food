<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'position',
    'start',
    'end',
    'hour_id',
])]

class Period extends Model
{
    //
}
