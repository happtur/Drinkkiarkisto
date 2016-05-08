<?php

class Ingredient extends BaseModel {
	public $id, $name, $amount, $validators;

	public function __construct($attributes) {
		parent::__construct($attributes);

		$this->name = strtolower($this->name);
		$this->id = self::get_id($this->name);

		$this->validators = array('validate_name', 'validate_amount');
	}


	public function add_to_recipe($recipe_id) {
		$this->save_if_needed();

		$query = DB::connection()->prepare('INSERT INTO Recipe_ingredient (recipe, ingredient, amount) VALUES (:recipe, :ingredient, :amount);');
		$query->execute(array('recipe' => $recipe_id, 'ingredient' => $this->id, 'amount' => $this->amount));
	}

	public function save_if_needed() {
		if($this->id == -1) {
			$query = DB::connection()->prepare('INSERT INTO Ingredient (name) 
				VALUES (:name) RETURNING id;');
			$query->execute(array('name' => $this->name));

			$row = $query->fetch();
			$this->id = $row['id'];
		}
	}


	public static function all($approved_only) {

		if($approved_only) {
			$query = DB::connection()->prepare('SELECT DISTINCT Ingredient.* 
				FROM Ingredient 
				INNER JOIN Recipe_ingredient
					 ON Recipe_ingredient.ingredient = Ingredient.id 
				INNER JOIN Recipe
					 ON Recipe_ingredient.recipe = Recipe.id 
				WHERE Recipe.approved = true
				ORDER BY Ingredient.name;');

		} else {
			$query = DB::connection()->prepare('SELECT * FROM Ingredient;');
		}

		$query->execute();
		$rows = $query->fetchAll();

		$ingredients = array();
		foreach ($rows as $row) {
			$ingredients[] = new Ingredient(array('id' => $row['id'], 'name' => $row['name']));
		}

		return $ingredients;
	}


	public static function delete_all_inactive() {
		$query = DB::connection()->prepare('DELETE FROM Ingredient WHERE id NOT IN (SELECT DISTINCT ingredient FROM Recipe_ingredient);');
		$query->execute();
	}


	public static function get_id($name) {
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