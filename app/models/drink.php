<?php

class Recipe extends BaseModel {
	public $id, $name, $category, $instructions;

	public function __construct($attributes) {
		parent::__construct($attributes);
	}

	public static function findAll() {
		$query = DB::connection() -> prepare('SELECT * FROM Recipe;');
		$query -> execute();

		$rows = $query -> fetchAll();
		$recipes = array();

//category is an int
		foreach($rows as $row) {
			$recipes[] = new Recipe(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'category' => $row['category'],
				'instructions' => $row['instructions']));
		}

		return $recipes;
	}

	public static function findOne($id) {
		$query = DB::connection() -> prepare('SELECT * FROM Recipe WHERE id = :id;');

		$query -> execute(array('id' => $id));
		$row = $query -> fetch();

		if($row) {
			$recipe = new Recipe(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'category' => $row['category'],
				'instructions' => $row['instructions']));

			return $recipe;
		}

		return null;
	}
}