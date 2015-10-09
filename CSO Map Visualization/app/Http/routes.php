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

Route::get('/', 'TasksController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

// REST v1
Route::get('REST/v1/timeclock/mapdata', 'RESTController@getMapdata');


Route::get('timeclock', 'TimeclockController@index');
Route::get('ccf', 'CCFController@index');
Route::get('tasks', 'TasksController@index');
Route::get('RT', 'RTController@index');
Route::get('stats', 'StatsController@index');
Route::get('dev/environment', 'DevController@environment');
Route::post('tasks', 'TasksController@post');

//CSO Map view
Route::get('csomap','HomeController@csomap');

