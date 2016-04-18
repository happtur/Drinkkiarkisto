<?php

class Ingredient extends BaseModel {
	public $id, $name, $amount, $validators;

	public function __construct($attributes) {
		parent::__construct($attributes);
		$this->id = self::getId($this->name);

		$this->validators = array('validate_name', 'validate_amount');
	}

	//make case insensitive (unless you can get citext to work :P)
	//could limit amount ? count, ml, g, (NB '2 slices' + 'lemon' -> '2' + 'lemon slices', so no..)
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
			$errors[] = "The amount field cannot be empty";
		}

		return $errors;
	}

}