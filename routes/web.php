<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\NamedRoutesController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SpaController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Laravel\Lumen\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('routes', ['as' => 'routes', 'uses' => NamedRoutesController::class . '@get']);
$router->post('register', ['uses' => RegisterController::class . '@register', 'as' => 'register']);

$router->group(['prefix' => 'auth', 'as' => 'auth'], function () use ($router) {
    $router->post('login', ['uses' => AuthController::class . '@login', 'as' => 'login']);
    $router->post('refresh', ['uses' => AuthController::class . '@refresh', 'as' => 'refresh']);
});

$router->group(['prefix' => 'users', 'as' => 'users'], function () use ($router) {
    $router->get('self', ['uses' => UserController::class . '@getSelf', 'as' => 'getSelf']);
});
