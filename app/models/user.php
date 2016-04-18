<?php

class User extends BaseModel() {
	//boolean admin? - also in database
	public $id, $name, $password, $validators;

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

	public static function authenticate($name, $password) {
		$query = DB::connection()->prepare('SELECT * FROM Service_user WHERE name= :name AND password= :password LIMIT 1;');
		$query->execute(array('name' => $name, 'password' => $password));
		$row = $query->fetch();

		if($row) {
			return new User(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'password' => $row['password']));

		} else {
			return null;
		}
	}

	public static function find($id) {
		$query = DB::connection()->prepare('SELECT * FROM Service_user WHERE id= :id LIMIT 1;');
		$query->execute(array('id' => $id));

		$row = $query->fetch();
		if($row) {
			return new User(array(
				'id' => $id,
				'name' => $row['name'],
				'password' => $row['password']));
		}

		return null;
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
			$errors[] = "Password has to have at least 6 characters"
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