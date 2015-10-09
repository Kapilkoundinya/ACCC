<?php namespace App\Http\Controllers;

class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$sayings = array('Have a wonderful day!', 'Howdy!', 'You are awesome!', 'Be the best you can be!', 'Hope you are doing Fabulous!', 'Be confident and attentive!');
		return view('home')->with('sayings', $sayings[array_rand($sayings)]);
	}

        public function csomap()
	{
		return view('cso-map.csomap');

	}
}
