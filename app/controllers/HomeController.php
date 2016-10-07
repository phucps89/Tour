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
		$minSupp  = 50;                  //minimal support
		$minConf  = 90;                 //minimal confidence
		$type     = Apriori::SRC_PLAIN; //data type
		$recomFor = 'beer';             //recommendation for
		$dataFile = 'data.json.gz';     //file for saving of state
//transactions
		$file = fopen(public_path('db/1000.inp.txt'), 'r');
		$data = [];
		while(!feof($file)){
			$line = fgets($file);
			if($line) {
				$db = explode(' ',$line);
				if($db){
					$data[] = implode(',', $db);
				}
			}
		}

//		$data = array(
//			'bread, milk',
//			'sugar, milk, beer',
//			'bread',
//			'bread, milk, beer',
//			'sugar, milk, beer'
//		); //id(items)
		try {
			$start = microtime(true);
			$apri = new Apriori($type, $data, $minSupp, $minConf);
			$apri
				->solve()
				->generateRules()
				->displayRules();                 //save state with rules
			$time_elapsed_secs = microtime(true) - $start;
			echo 'Time: '.$time_elapsed_secs;
		} catch (Exception $exc) {
			echo $exc->getMessage();
		}

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
