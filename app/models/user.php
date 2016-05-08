<?php

class User extends BaseModel {
	public $id, $name, $password, $admin, $recipes_added, $validators;

	public function __construct($attributes) {
		parent::__construct($attributes);

		$this->validators = array('validate_name', 'validate_password');
	}

	public function save() {
		$query = DB::connection()->prepare('INSERT INTO Service_user (name, password) VALUES (:name, :password) RETURNING id;');
		$query->execute(array('name' => $this->name, 'password' => $this->password));

		$row = $query->fetch();
		$this->id = $row['id'];
	}

	public function authenticate($password) {
		return $this->password == $password;
	}

	public static function authenticate_name($name) {
		$query = DB::connection()->prepare('SELECT * FROM Service_user WHERE name= :name LIMIT 1;');
		$query->execute(array('name' => $name));
		$row = $query->fetch();

		if($row) {

			$admin = false;
			if($row['admin'] == 't') {
				$admin = true;
			}

			return new User(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'password' => $row['password'],
				'admin' => $admin));

		} else {
			return null;
		}
	}

	public static function find($id) {
		$query = DB::connection()->prepare('SELECT * FROM Service_user WHERE id= :id LIMIT 1;');
		$query->execute(array('id' => $id));

		$row = $query->fetch();
		if($row) {

			//???
			$admin = false;
			if($row['admin'] == 't') {
				$admin = true;
			}

			return new User(array(
				'id' => $id,
				'name' => $row['name'],
				'admin' => $admin));
		}

		return null;
	}

	public static function all() {
		$query = DB::connection()->prepare('SELECT Service_user.id AS id, Service_user.name AS name, Service_user.admin AS admin, Temp.recipes_added AS recipes_added 
			FROM Service_user 
			LEFT JOIN (SELECT added_by, COUNT(*) AS recipes_added FROM Recipe WHERE approved = true GROUP BY added_by) AS Temp ON Temp.added_by = Service_user.id
			ORDER BY recipes_added DESC;');
		$query->execute();
		$rows = $query->fetchAll();

		$users = array();

		foreach ($rows as $row) {

			$admin = false;
			if($row['admin'] == 't') {
				$admin = true;
			}

			if($row['recipes_added']) {
				$recipes_added = $row['recipes_added'];
			} else {
				$recipes_added = 0;
			}

			$users[] = new User(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'admin' => $admin,
				'recipes_added' => $recipes_added));
		}

		return $users;
	}

	public function delete() {
		$query = DB::connection()->prepare('UPDATE Recipe SET added_by = NULL WHERE added_by = :id;');
		$query->execute(array('id' => $this->id));

		$query = DB::connection()->prepare('DELETE FROM Service_user WHERE id = :id;');
		$query->execute(array('id' => $this->id));
	}

	public static function make_admin($id) {
		$query = DB::connection()->prepare('UPDATE Service_user SET admin = true WHERE id = :id;');
		$query->execute(array('id' => $id));

		$query = DB::connection()->prepare('UPDATE Recipe SET approved = true WHERE added_by = :id;');
		$query->execute(array('id' => $id));
	}

	public function change_password() {
		$query = DB::connection()->prepare('UPDATE Service_user SET password = :password WHERE id = :id;');
		$query->execute(array('password' => $this->password, 'id' => $this->id));
	}

	public function contributions() {
		$approved = $this->contributions_that_are(true);
		$pending = $this->contributions_that_are(false);
		
		$recipes = array('approved' => $approved, 'pending' => $pending);

		return $recipes;
	}

	private function contributions_that_are($approved_status) {
		$recipes = array();

		if($approved_status){
			$query = DB::connection()->prepare('SELECT id, name FROM Recipe WHERE added_by = :id AND approved = true;');
		} else {
			$query = DB::connection()->prepare('SELECT id, name FROM Recipe WHERE added_by = :id AND approved = false;');
		}

		$query->execute(array('id' => $this->id));
		$rows = $query->fetchAll();

		foreach ($rows as $row) {
			$recipes[] = new Recipe(array('id' => $row['id'], 'name' => $row['name']));
		}

		return $recipes;
	}


	public function validate_name() {
		$errors = array();

		if(!parent::validate_string_length($this->name, 1)) {
			$errors[] = "Username can't be empty";
		} else if(!$this->name_available()) {
			$errors[] = "That username is not available";
		}
		
		return $errors;
	}

	public function validate_password() {
		$errors = array();

		if(!parent::validate_string_length($this->password, 6)) {
			$errors[] = "Password has to have at least 6 characters";
		}
		
		return $errors;
	}

	private function name_available() {
		$query = DB::connection()->prepare('SELECT * FROM Service_user WHERE name= :name;');
		$query->execute(array('name' => $this->name));

		$row = $query->fetch();
		if($row) {
			return false;
		}

		return true;
	}
}