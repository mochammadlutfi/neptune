<?php

namespace App\Models;

use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    use NodeTrait;

    protected $table = 'menus';

    protected $fillable = [
        'name', 'icon', 'to', 'module', 'permission',
    ];
    
}
