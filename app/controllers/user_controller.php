<?php

class UserController extends BaseController {

	public static function login() {
		View::make('user/login.html');
	}

	public static function handle_login() {
		//password crypt()
		$params = $_POST;
		$errors = array();

		$user = User::authenticate_name($params['name']);
		if(is_null($user)) {
			$errors[] = "There is no user with that name";

		} else {
			if(!$user->authenticate($params['password'])) {
				$errors[] = "The password you entered is not correct";
			}
		}

		if(count($errors) != 0) {
			View::make('user/login.html', array('errors' => $errors, 'username' => $params['name']));

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


	public static function save() {
		$params = $_POST;

		$password = $params['password1'];
		$user = new User(array('name' => $params['name'], 'password' => $password));
		$errors = $user->errors();
		$errors = array_merge($errors, self::check_if_same($password, $_POST['password2']));

		if(count($errors) == 0) {
			$user->save();
			$_SESSION['user'] = $user->id;
			Redirect::to('/', array('success' => 'User successfully added'));

		} else {
			View::make('user/new_user.html', array('errors' => $errors, 'username' => $user->name));
		}
	}

	public static function all() {
		self::check_logged_in_is_admin();

		$users = User::all();
		View::make('user/list.html', array('users' => $users));
	}

	public static function delete($id) {
		self::check_logged_in_is_admin_or_has_id($id);

		$user = User::find($id);
		$user->delete();
		Redirect::to('/', array('success' => 'User ' . $user->name . ' was successfully deleted'));
	}

	public static function make_admin($id) {
		self::check_logged_in_is_admin();

		User::make_admin($id);
		Redirect::to('/user/' . $id, array('success' => 'User was successfully made an admin'));
	}


	public static function show($id) {
		self::check_logged_in_is_admin_or_has_id($id);

		$user = User::find($id);
		$recipes = $user->contributions();

		View::make('user/user.html', array('user' => $user, 'approved' => $recipes['approved'], 'pending' => $recipes['pending']));
	}

	public static function change_password($id) {
		$user = self::get_user_logged_in();
		if($user->id != $id) {
			Redirect::to('/login', array('message' => "You can't change the password unless you're logged in to that account!"));
		}

		$password = $_POST['password1'];

		$user = new User(array('id' => $id, 'password' => $password));
		$errors = $user->validate_password();
		$errors = array_merge($errors, self::check_if_same($password, $_POST['password2']));

		if(count($errors) == 0) {
			$user->change_password();
			Redirect::to('/user/' . $id, array('success' => 'Password was changed'));

		} else {
			$user = User::find($id);
			$recipes = $user->contributions();
			View::make('/user/user.html', array('errors' => $errors, 'user' => $user, 'approved' => $recipes['approved'], 'pending' => $recipes['pending']));
		}
	}

	private static function check_if_same($first, $second) {
		$errors = array();

		if(strcmp($first, $second) != 0) {
			$errors[] = "The passwords didn't match";
		}

		return $errors;
	}

}