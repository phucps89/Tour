<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function index()
	{
		return View::make('home.index');
	}

	public function main(){
		$defaultQuestions = Question::whereIn('id', [1,2,3,4])->get();
		return View::make('home.main', compact('defaultQuestions'));
	}

	public function login(){
		if(Request::isMethod('post')){
			return $this->postLogin();
		}
		return View::make('login');
	}

	private function postLogin(){
		if (Auth::attempt(array(
				'email' => Input::get('email'),
				'password' => Input::get('password')
		)))
		{
			return Redirect::route('home');
		}
		else{
			return Redirect::back();
		}
	}

	public function register(){
		try {
			$user = new User();
			$user->first_name = Input::get('first_name');
			$user->last_name = Input::get('last_name');
			$user->email = Input::get('email');
			$user->password = Hash::make(Input::get('password'));
			$user->save();
			Auth::loginUsingId($user->id);
				return Redirect::route('home');
		}
		catch(Exception $e){
			return Redirect::back()
					->withInput(Input::except('password'))
					->with('error', 'Can not register. Please try again!');
		}
	}
}
