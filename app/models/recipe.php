<?php

class Recipe extends BaseModel {
	public $id, $name, $category, $instructions, $numberOfIngredients, $ingredients;

	public function __construct($attributes) {
		parent::__construct($attributes);
	}

	//to be changed.. particularly the input (-> (?textfield with? )dropdown menu or sthg)
	//move to category-class?
	public static function category($category) {
		$query = DB::connection() -> prepare('SELECT id FROM Category WHERE name = :category LIMIT 1');
		$query -> execute(array('category' => $category));

		$row = $query->fetch();

		if($row) {
			return $row['id'];
		}
		//else??
	}

	public function save() {
		//add what to do if there's already a drink with that name.

		$query = DB::connection() -> prepare('INSERT INTO Recipe (name, category, instructions) VALUES (:name, :category, :instructions) RETURNING id;');
		$query -> execute(array('name' => $this->name, 'category' => $this->category, 'instructions' => $this->instructions));

		$row = $query -> fetch();
		$this->id = $row['id'];

	}

	public function addIngredient($ingredient) {
		$ingId = $ingredient->getId();

		if($ingId == -1) {

			$query = DB::connection() -> prepare('INSERT INTO Ingredient (name) VALUES (:ingredient) RETURNING id;');
			$query -> execute(array('ingredient' => $ingredient->name));

			$row = $query->fetch();
			$ingId = $row['id'];
		}

		$query = DB::connection() -> prepare('INSERT INTO Recipe_ingredient (recipe, ingredient, amount) VALUES (:id, :ingredient, :amount)');
		$query -> execute(array('id' => $this->id, 'ingredient' => $ingId, 'amount' => $ingredient->amount));

		$ingredients = $this->ingredients;
		$ingredients[] = $ingredient;
	}
	

	public static function findAll() {
		$query = DB::connection() -> prepare('SELECT Recipe.id AS id, Recipe.name AS name, Category.name AS category, Recipe.instructions AS instructions, Ingredients.number_of_ingredients AS number_of_ingredients FROM Recipe LEFT JOIN Category ON Recipe.category = Category.id LEFT JOIN (SELECT recipe, COUNT(*) AS number_of_ingredients FROM Recipe_ingredient GROUP BY recipe) AS Ingredients ON Ingredients.recipe = Recipe.id;');
		$query -> execute();

		$rows = $query -> fetchAll();
		$recipes = array();

		foreach($rows as $row) {
			$recipes[] = new Recipe(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'category' => $row['category'],
				'instructions' => $row['instructions'],
				'numberOfIngredients' => $row['number_of_ingredients']));
		}

		return $recipes;
	}


	//combined
	public static function findOne($id) {
		$query = DB::connection() -> prepare('SELECT Recipe.id AS id, Recipe.name AS name, Recipe.instructions AS instructions, Category.name AS category, Recipe_ingredient.amount AS amount, Ingredient.name AS ingredient
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id
			LEFT JOIN Recipe_ingredient ON Recipe.id = Recipe_ingredient.recipe
			LEFT JOIN Ingredient ON Recipe_ingredient.ingredient = Ingredient.id
			WHERE Recipe.id = :id;');

		$query -> execute(array('id' => $id));
		$rows = $query -> fetchAll();

		$ingredients = array();

		//what to do if there is no drink with the given id
		foreach($rows as $row) {
			if($row['ingredient'] != null) {
				$ingredients[] = new Ingredient(array('name' => $row['ingredient'], 'amount' => $row['amount']));
			}
			
			//should do this for one row only
			$name = $row['name'];
			$category = $row['category'];
			$instructions =  $row['instructions'];
		}
		

		$recipe = new Recipe(array(
			'id' => $id,
			'name' => $name,
			'category' => $category,
			'instructions' => $instructions,
			'ingredients' => $ingredients
			));

		return $recipe;
		

		return null;
	}




	// //apart
	// public static function findOne($id) {
	// 	$query = DB::connection() -> prepare('SELECT Recipe.id AS id, Recipe.name AS name, Recipe.instructions AS instructions, Category.name AS category 
	// 		FROM Recipe 
	// 		LEFT JOIN Category ON Recipe.category = Category.id
	// 		WHERE Recipe.id = :id;');

	// 	$query -> execute(array('id' => $id));
	// 	$row = $query -> fetch();

	// 	if($row) {
	// 		$id = $row['id'];
	// 		//$ingredientsAmounts = this -> ingredients($id);

	// 		$recipe = new Recipe(array(
	// 			'id' => $id,
	// 			'name' => $row['name'],
	// 			'category' => $row['category'],
	// 			'instructions' => $row['instructions'],
	//			'ingredientAmounts' => this -> ingredients($id)
	// 			//'ingredients' => array_keys($ingredientsAmounts),
	// 			//'amounts' => array_values($ingredientsAmounts)
	// 			));

	// 		return $recipe;
	// 	}

	// 	return null;
	// }

	// private static function ingredients($id) {
	// 	$query = DB::connection() -> prepare('SELECT Ingredient.name AS ingredient, RecipeIngredient.amount AS amount 
	// 		FROM RecipeIngredient 
	// 		LEFT JOIN Ingredient ON RecipeIngredient.ingredient = Ingredient.id 
	// 		WHERE RecipeIngredient.recipe = :id;');

	// 	$query -> execute(array('id' => $id));
	// 	$rows = $query -> fetchAll();

	// 	$ingredients = array();

	// 	foreach($rows as $row) {

	// 		$ingredient = (string) $row['ingredient'];
	// 		array[] = $ingredient => $row['amount']
	// 	}

	// 	return $ingredients;
	// }
}