<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SpaController;
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

$router->get('phpinfo', function() {
  return phpinfo();
});

$router->get('{all:.*}', SpaController::class . '@index');

$router->group(['prefix' => 'api'], function() use ($router) {
  $router->post('register', RegisterController::class . '@register');
});