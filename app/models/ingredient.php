<?php

class Ingredient extends BaseModel {
	public $id, $name, $amount, $validators;

	public function __construct($attributes) {
		parent::__construct($attributes);

		$this->name = strtolower($this->name);
		$this->id = self::getId($this->name);

		$this->validators = array('validate_name', 'validate_amount');
	}

	public function saveIfNeeded() {
		if($this->id == -1) {
			$query = DB::connection()->prepare('INSERT INTO Ingredient (name) 
				VALUES (:name) RETURNING id;');
			$query->execute(array('name' => $this->name));

			$row = $query->fetch();
			$this->id = $row['id'];
		}
	}

	//needed only when useless, i.e. not in Recipe_ingredient. So this function assumes that that's the case..
	public function delete() {
		$query = DB::connection()->prepare('DELETE FROM Ingredient WHERE id = :id;');
		$query->execute(array('id' => $this->id));
	}

	public static function getId($name) {
		$query = DB::connection() -> prepare('SELECT id FROM Ingredient WHERE name = :name;');
		$query -> execute(array('name' => $name));

		$row = $query->fetch();
		if($row) {
			return $row['id'];
		} else {
			return -1;
		}
	}

	public function validate_name() {
		$errors = array();

		if(!$this->validate_string_length($this->name, 3)) {
			$errors[] = "The ingredient's name must consist of at least three characters";
		}

		return $errors;
	}

	public function validate_amount() {
		$errors = array();

		if(!$this->validate_string_length($this->amount, 1)) {
			$errors[] = "Amount cannot be empty";
		}

		return $errors;
	}

}