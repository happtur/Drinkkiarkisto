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
		//check if admin

		$users = User::all();
		View::make('user/list', array('users' => $users));
	}

	//if you want the name displayed make user-object...
	public static function delete($id) {
		//check if admin

		User::delete($id);
		Redirect::to('/', array('success' => 'User was successfully deleted'));
	}

	//if you want the name displayed....
	public static function make_admin($id) {
		//check if admin

		User::make_admin($id);
		Redirect::to('/', array('success' => 'User was successfully made an admin'));
	}

	//do I want this?
		//no: remove from routes
		//yes: add links to user/list.html
	public static function show_user($id) {
		//check if admin or user in question

		//user.html (name, adminstatus, added recipes, change password(only user in question))
	}

}