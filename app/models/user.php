<?php

class User extends BaseModel {
	//boolean admin database to php?
	public $id, $name, $password, $admin, $recipes_added, $validators;

	public function __construct($attributes) {
		parent::__construct($attributes);

		$this->validators = array('validate_name', 'validate_password');
	}

	//save
	//update (change password, change adminstatus)
	//authenticate
	//delete
	//findOne
	//validate (name, password)
	//changePassword
	//changeAdminStatus
	//nameAvailable
	//listAll (+amount of contributions)

	public function save() {
		$query = DB::connection()->prepare('INSERT INTO Service_user (name, password) VALUES (:name, :password) RETURNING id;');
		$query->execute(array('name' => $this->name, 'password' => $this->password));

		$row = $query->fetch();
		$this->id = $row['id'];
	}

	//admin ???
	public static function authenticate($name, $password) {
		$query = DB::connection()->prepare('SELECT * FROM Service_user WHERE name= :name AND password= :password LIMIT 1;');
		$query->execute(array('name' => $name, 'password' => $password));
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
				'password' => $row['password'],
				'admin' => $admin));
		}

		return null;
	}

	public static function all() {
		$query = DB::connection()->prepare('SELECT Service_user.name AS name, Service_user.admin AS admin, Temp.recipes_added AS recipes_added 
			FROM Service_user 
			LEFT JOIN (SELECT added_by, COUNT(*) AS recipes_added FROM Recipe GROUP BY added_by) AS Temp ON Temp.added_by = Service_user.id;');
		$query->execute();
		$rows = $query->fetchAll();

		$users = array();

		//boolean admin!
		foreach ($rows as $row) {

			$admin = false;
			if($row['admin'] == 't') {
				$admin = true;
			}

			$users[] = new User(array(
				'name' => $row['name'],
				'admin' => $admin,
				'recipes_added' => $row['recipes_added']));
		}

		return $users;
	}

	public static function delete($id) {
		$query = DB::connection()->prepare('UPDATE Recipe SET added_by = NULL WHERE added_by = :id;');
		$query->execute(array('id' => $id));

		$query = DB::connection()->prepare('DELETE FROM Service_user WHERE id = :id;');
		$query->execute(array('id') => $id);
	}

	public static function make_admin($id) {
		$query = DB::connection()->prepare('UPDATE Service_user SET admin = true WHERE id = :id');
		$query->execute(array('id' => $id));
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