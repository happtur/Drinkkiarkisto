<?php

class Ingredient extends BaseModel {
	public $name, $amount;

	public function __construct($attributes) {
		parent::__construct($attributes);
	}

	//make case insensitive (unless you can get citext to work :P)
	public function getId() {
		$query = DB::connection() -> prepare('SELECT id FROM Ingredient WHERE name = :name;');
		$query -> execute(array('name' => $this->name));

		$row = $query->fetch();
		if($row) {
			return $row['id'];
		} else {
			return -1;
		}

	}

}