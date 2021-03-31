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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'] , function($router){
	// $router->get('/register', 'RegisterController@index');
	// $router->get('/register/{id}', 'RegisterController@show');
	$router->post('/register/save', 'RegisterController@store');
	// $router->post('/register/{id}/update', 'RegisterController@update');
	// $router->post('/register/{id}/delete', 'RegisterController@destroy');

	$router->post('/login', ['uses' => 'RegisterController@authenticate', 'as' => 'login']);
});

$router->group(['middleware' => ['auth', 'verified']], function () use ($router) {
	$router->post('/logout', 'AuthController@logout');
  	$router->get('/user', 'AuthController@user');
});

$router->get('email/verify/{id}', 'VerificationController@verify');

$router->post('password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
$router->post('password/reset', [ 'as' => 'password.reset', 'uses' => 'ResetPasswordController@reset' ]);
