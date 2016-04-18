<?php

class UserController extends BaseController {

	public static function login() {
		View::make('user/login.html');
	}

	public static function handle_login() {
		//password crypt()
		$params = $_POST;

		$user = User::authenticate($params['name'], $params['password']);

		if(is_null($user)) {
			View::make('user/login.html', array('error' => "The password doesn't match the username", 'username' => $params['name']));

		} else {
			$_SESSION['user'] = $user->id;
			Redirect::to('/', array('success' => 'Welcome back ' . $user->name));
		}
	}

	public static function logout() {
		$_SESSION['user'] = null;
		Redirect::to('/', array('message' => 'Redirected, hah, now how does that feel? Maybe you think twice about logging out next time...'));
	}

//add to routes (newuser), create view (can combine log in and add, buttontext and heading as parameter)
	// public static function handle_add_user() {
	// 	$params = $_POST;

	// 	$user = new User(array('name' => $params['name'], 'password' => $params['password']));

	// 	$errors = $user->errors();

	// 	if(count($errors) == 0) {
	// 		$user->save();
	//		Redirect::to('/', array('success' => 'User added successfully'))
	// 	} else {
	// 		Redirect::to('/newuser', array('errors' => $errors));
	// 	}
	// }

	// public static function new_user() {
	// 	View::make('user/new_user.html');
	// }

}