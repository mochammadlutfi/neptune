<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // private function errorbag($data){

    //     return (object) collect($data)->map(function ($errors) {
    //         return $errors[0];
    //     })->toArray();
    // }
}
