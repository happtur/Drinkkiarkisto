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
		Redirect::to('/', array('message' => 'Logged out'));
	}

	public static function new_user() {
		View::make('user/new_user.html');
	}


	public static function handle_new_user() {
		$params = $_POST;

		$user = new User(array('name' => $params['name'], 'password' => $params['password']));

		$errors = $user->errors();

		if(count($errors) == 0) {
			$user->save();
			$_SESSION['user'] = $user->id;
			Redirect::to('/', array('success' => 'User successfully added'));

		} else {
			View::make('user/new_user.html', array('errors' => $errors, 'username' => $user->name));
		}
	}

	//atm list shows number of all recipes, should change to approved/total?
	public static function list_all() {
		self::check_logged_in_is_admin();

		$users = User::all();
		View::make('user/list.html', array('users' => $users));
	}

	//if you want the name displayed make user-object...
	public static function delete($id) {
		self::check_logged_in_is_admin_or_has_id($id);

		User::delete($id);
		Redirect::to('/', array('success' => 'User was successfully deleted'));
	}

	//if you want the name displayed....
	public static function make_admin($id) {
		self::check_logged_in_is_admin();

		User::make_admin($id);
		Redirect::to('/', array('success' => 'User was successfully made an admin'));
	}


	public static function show_user($id) {
		self::check_logged_in_is_admin_or_has_id($id);

		$user = User::find($id);
		//find recipes
		View::make('user/user.html', array('user' => $user));
	}

	public static function change_password($id) {
		$user = self::get_user_logged_in();
		if($user->id != $id) {
			Redirect::to('/login', array('message' => "You can't change the password unless your logged in to that account!"));
		}

		$password = $_POST['password'];

		$user = new User(array('id' => $id, 'password' => $password));
		$errors = $user->validate_password();

		if(count($errors) == 0) {
			$user->change_password();
			Redirect::to('/user/' . $id, array('success' => 'Password was changed'));
		} else {
			$user = User::find($id);
			View::make('/user/user.html', array('errors' => $errors, 'user' => $user));
		}
	}

}