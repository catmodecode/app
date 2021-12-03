<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Laravel\Lumen\Routing\Router;

class NamedRoutesController extends Controller
{
    public function get(Router $router)
    {
        return response()->json($router->namedRoutes);
    }
}
