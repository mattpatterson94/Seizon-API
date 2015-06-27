<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//$app->get('/', function () use ($app) {
//    return $app->welcome();
//});

$app->get('/user', 'App\Resources\User\Controllers\UserController@index');
$app->get('/user/{id}', 'App\Resources\User\Controllers\UserController@get');
$app->post('/user', 'App\Resources\User\Controllers\UserController@store');
$app->put('/user/{id}', 'App\Resources\User\Controllers\UserController@update');
$app->delete('/user/{id}', 'App\Resources\User\Controllers\UserController@destroy');
