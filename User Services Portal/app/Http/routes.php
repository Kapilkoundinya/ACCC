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

Route::get('/', 'HomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

// REST v1
Route::get('REST/v1/timeclock/mapdata', 'RESTController@getMapdata');
Route::get('REST/v1/feedback/tickets', 'RESTController@getFeedbackGlance');

Route::get('timeclock', 'TimeclockController@index');
Route::get('tasks', 'TasksController@index');
Route::get('RT', 'RTController@index');
Route::get('stats', 'StatsController@index');
Route::get('dev/environment', 'DevController@environment');
Route::get('dev/roleinfo', 'DevController@roleinfo');
Route::get('dev/palette', 'DevController@palette');
Route::post('tasks', 'TasksController@post');
Route::get('ccf', 'ccfController@index');
Route::post('ccf_post', 'ccfController@post');
Route::post('get_conditions', 'ccfController@conditions');

//CSO Map view

Route::get('csomap','HomeController@csomap');

// Entrust Restrictions

Entrust::routeNeedsRole('dev/*', ['super-admin','super-user','developer'], Redirect::to('auth/bluestem'), false);


// Chase's Shenanigans
Route::get('ccf2', 'ccfController@index2');
Route::get('partials/ccfInteraction', 'ccfController@interactionPartial');

//AdminViewTest
Route::get('adminview','AdminViewController@adminview');

// Customer Feedback
Route::get('feedback/glance', 'FeedbackController@glance');
Route::get('feedback/emailtest/{rtid}', ['middleware' => 'auth', 'uses' => 'FeedbackController@emailtest']);
Route::get('feedback/{rtid}/view/{key}', 'FeedbackController@view');
Route::get('feedback/{rtid}/{rating}/{key}', 'FeedbackController@index');
Route::post('feedback/{rtid}/view/{key}', 'FeedbackController@view');
Route::post('feedback/{rtid}/{rating}/{key}', 'FeedbackController@index');

