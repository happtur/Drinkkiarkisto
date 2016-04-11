<?php

class Recipe extends BaseModel {
	public $id, $name, $category, $instructions, $numberOfIngredients, $ingredients, $amounts;

	public function __construct($attributes) {
		parent::__construct($attributes);
	}

	public static function findAll() {
		$query = DB::connection() -> prepare('SELECT Recipe.id AS id, Recipe.name AS name, Category.name AS category, Recipe.instructions AS instructions, COUNT(RecipeIngredient) AS numberOfIngredients 
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id 
			LEFT JOIN RecipeIngredient ON Recipe.id = RecipeIngredient.recipe
			GROUP BY Recipe.id;');
		$query -> execute();

		$rows = $query -> fetchAll();
		$recipes = array();

		foreach($rows as $row) {
			$recipes[] = new Recipe(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'category' => $row['category'],
				'instructions' => $row['instructions']
				'numberOfIngredients' => $row['numberOfIngredients']));
		}

		return $recipes;
	}


	//combined - not finished
	public static function findOne($id) {
		$query = DB::connection() -> prepare('SELECT Recipe.id AS id, Recipe.name AS name, Recipe.instructions AS instructions, Category.name AS category, RecipeIngredient.amount AS amount, Ingredient.name AS ingredient
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id
			INNER JOIN RecipeIngredient ON Recipe.id = RecipeIngredient.recipe
			INNER JOIN Ingredient ON RecipeIngredient.ingredient = Ingredient.id
			WHERE Recipe.id = :id;');

		$query -> execute(array('id' => $id));
		$rows = $query -> fetchAll();

		foreach($rows as $row) {
			$
		}
		
			$id = $row['id'];
			$ingredientsAmounts = this -> ingredients($id);

			$recipe = new Recipe(array(
				'id' => $id,
				'name' => $row['name'],
				'category' => $row['category'],
				'instructions' => $row['instructions'],
				'ingredients' => array_keys($ingredientsAmounts),
				'amounts' => array_values($ingredientsAmounts)
				));

			return $recipe;
		

		return null;
	}




	//apart
	public static function findOne($id) {
		$query = DB::connection() -> prepare('SELECT Recipe.id AS id, Recipe.name AS name, Recipe.instructions AS instructions, Category.name AS category 
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id
			WHERE Recipe.id = :id;');

		$query -> execute(array('id' => $id));
		$row = $query -> fetch();

		if($row) {
			$id = $row['id'];
			$ingredientsAmounts = this -> ingredients($id);

			$recipe = new Recipe(array(
				'id' => $id,
				'name' => $row['name'],
				'category' => $row['category'],
				'instructions' => $row['instructions'],
				'ingredients' => array_keys($ingredientsAmounts),
				'amounts' => array_values($ingredientsAmounts)
				));

			return $recipe;
		}

		return null;
	}

	private static function ingredients($id) {
		$query = DB::connection() -> prepare('SELECT Ingredient.name AS ingredient, RecipeIngredient.amount AS amount 
			FROM RecipeIngredient 
			LEFT JOIN Ingredient ON RecipeIngredient.ingredient = Ingredient.id 
			WHERE RecipeIngredient.recipe = :id;');

		$query -> execute(array('id' => $id));
		$rows = $query -> fetchAll();

		$ingredients = array();

		foreach($rows as $row) {

			$ingredient = (string) $row['ingredient'];
			array[] = $ingredient => $row['amount']
		}

		return $ingredients;
	}
}