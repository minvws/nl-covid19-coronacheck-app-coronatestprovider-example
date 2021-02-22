<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', ['uses' => 'Controller@index']);

$router->post('/ctp/get_test_result', ['middleware' =>'cms_sign','uses' => 'CtpApiController@get_test_result']);

$router->get('/apitest', ['uses' => 'ApiTestController@index']);

$router->group(['prefix' => 'test_result'], function () use ($router) {
    $router->get('',['uses' => 'TestResultController@view']);
    $router->get('create',['uses' => 'TestResultController@createForm']);
    $router->post('create',['uses' => 'TestResultController@displayCreated']);
    $router->get('view',['uses' => 'TestResultController@view']);
});
