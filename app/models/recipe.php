<?php

class Recipe extends BaseModel {
	public $id, $name, $category, $instructions, $added_by, $ingredients, $numberOfIngredients, $validators;

	public function __construct($attributes) {
		parent::__construct($attributes);

		$this->validators = array('validate_name', 'validate_instructions');
	}

	//static findAll (alphabetical order) APPROVED
	//static findAll (list by amount of ingredients) APPROVED
	//static findAll (in category) APPROVED


	public function save($approved) {
		$category_id = Category::getId($this->category);

		$query = DB::connection() -> prepare('INSERT INTO Recipe (name, category, instructions, approved, added_by) VALUES (:name, :category, :instructions, :approved, :added_by) RETURNING id;');
		$query -> execute(array('name' => $this->name, 'category' => $category_id, 'instructions' => $this->instructions, 'approved' => $approved, 'added_by' => $this->added_by));

		$row = $query -> fetch();
		$this->id = $row['id'];

		$this->saveIngredients();
	}

	//removes all, adds all. Even if tiny change...
	public function update() {
		$query = DB::connection()->prepare('DELETE FROM Recipe_ingredient WHERE recipe = :id;');
		$query->execute(array('id' => $this->id));

		$category = Category::getId($this->category);

		$query = DB::connection()->prepare('UPDATE Recipe SET name = :name, category = :category, instructions = :instructions WHERE id = :id;');
		$query->execute(array('name' => $this->name, 'category' => $category, 'instructions' => $this->instructions, 'id' => $this->id));

		$this->saveIngredients();
	}

	public function saveIngredients() {

		foreach ($this->ingredients as $ingredient) {
			$ingredient->saveIfNeeded();

			//move to ingredient? $ingredient->addToRecipe($recipe_id)
			$query = DB::connection()->prepare('INSERT INTO Recipe_ingredient (recipe, ingredient, amount) VALUES (:recipe, :ingredient, :amount);');
			$query->execute(array('recipe' => $this->id, 'ingredient' => $ingredient->name, 'amount' => $ingredient->amount));
		}
	}

	//to static?
	public function delete() {
		$query = DB::connection()->prepare('DELETE FROM Recipe_ingredient WHERE recipe = :id;');
		$query->execute(array('id' => $this->id));

		$query = DB::connection()->prepare('DELETE FROM Recipe WHERE id = :id;');
		$query->execute(array('id' => $this->id));
	}


	//only returns approved recipes
	//to implement: order by
	public static function findAll() {
		//boolean...
		return self::allWithApprovedStatus(true);
	}


	public static function findOne($id) {
		$query = DB::connection() -> prepare('SELECT Recipe.id AS id, Recipe.name AS name, Recipe.instructions AS instructions, Category.name AS category, Recipe_ingredient.amount AS amount, Ingredient.name AS ingredient
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id
			LEFT JOIN Recipe_ingredient ON Recipe.id = Recipe_ingredient.recipe
			LEFT JOIN Ingredient ON Recipe_ingredient.ingredient = Ingredient.id
			WHERE Recipe.id = :id;');

		$query -> execute(array('id' => $id));

//1.
		$row = $query->fetch();
		$ingredients = array();

		$name = $row['name'];
		$category = $row['category'];
		$instructions = $row['instructions'];

		while($row) {
			if($row['ingredient'] != null) {
				$ingredients[] = new Ingredient(array('name' => $row['ingredient'], 'amount' => $row['amount']));
			}
			$row = $query->fetch();
		}

//2.
		// $rows = $query -> fetchAll();

		// $ingredients = array();

		// //what to do if there is no drink with the given id
		// foreach($rows as $row) {
		// 	if($row['ingredient'] != null) {
		// 		$ingredients[] = new Ingredient(array('name' => $row['ingredient'], 'amount' => $row['amount']));
		// 	}
			
		// 	//should do this for one row only
		// 	$name = $row['name'];
		// 	$category = $row['category'];
		// 	$instructions =  $row['instructions'];
		// }
//end
		

		$recipe = new Recipe(array(
			'id' => $id,
			'name' => $name,
			'category' => $category,
			'instructions' => $instructions,
			'ingredients' => $ingredients
			));

		return $recipe;
	}


	public static function suggestions() {
		//boolean
		return self::allWithApprovedStatus(false);
	}

	//why instructions?
	private static function allWithApprovedStatus($approved_status) {
		$query = DB::connection() -> prepare('SELECT Recipe.id AS id, Recipe.name AS name, Category.name AS category, Ingredients.number_of_ingredients AS number_of_ingredients 
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id 
			LEFT JOIN 
			(SELECT recipe, COUNT(*) AS number_of_ingredients FROM Recipe_ingredient GROUP BY recipe) AS Ingredients 
			ON Ingredients.recipe = Recipe.id 
			WHERE Recipe.approved = :status;');
		$query -> execute(array('status' => $approved_status));

		$rows = $query -> fetchAll();
		$recipes = array();

		foreach($rows as $row) {
			$recipes[] = new Recipe(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'category' => $row['category'],
				'numberOfIngredients' => $row['number_of_ingredients']));
		}

		return $recipes;
	}


	public static function approve($id) {
		$query = DB::connection()->prepare('UPDATE Recipe SET approved = true WHERE id = :id;');
		$query->execute(array('id' => $id));
	}

	public static function isApproved($id) {
		$query = DB::connection()->prepare('SELECT approved FROM Recipe WHERE id = :id;');
		$query->execute(array('id' => $id));
		$row = $query->fetch();

		//check what $row['approved'] returns! 
		if($row and $row['approved'] == 't') {
			return true;
		}

		return false;
	}


	public function validate_name() {
		$errors = array();

		if (!$this->validate_string_length($this->name, 1)) {
			$errors[] = 'The drink must have a name';

		} elseif (!$this->name_available()) {
			$errors[] = "There's already a drink with that name";

		}

		return $errors;
	}

	private function name_available() {
		$query = DB::connection() -> prepare('SELECT id FROM Recipe 
			WHERE name = :name LIMIT 1;');
		$query -> execute(array('name' => $this->name));

		$row = $query->fetch();
		if($row and $row['id'] != $this->id) {
			return false;
		}
		
		return true;
		
	}

	public function validate_instructions() {
		$errors = array();

		if(!$this->validate_string_length($this->instructions, 3)) {
			$errors[] = 'The instructions must consist of at least three characters';
		}

		return $errors;

	}

}