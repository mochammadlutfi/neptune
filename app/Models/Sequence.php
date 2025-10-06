<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prefix',
        'date_format',
        'current_number',
        'padding',
        'reset_period',
        'last_reset_date',
        'separator'
    ];
}
