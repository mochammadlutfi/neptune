<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVessel extends Model
{
    use HasFactory;

    protected $table = 'user_vessels';
    
    protected $fillable = [
        'user_id',
        'vessel_id',
    ];


}
