<?php


//category: String, int or Category?
//Suggestion extends Recipe? would be suggestions, save and approve, no use really? shortens this monster...  
class Recipe extends BaseModel {
	public $id, $name, $category, $instructions, $added_by, $ingredients, $numberOfIngredients, $validators;

	public function __construct($attributes) {
		parent::__construct($attributes);

		$this->validators = array('validate_name', 'validate_instructions', 'validate_ingredients');
	}


	public function save($approved) {
		$category_id = Category::getId($this->category);

		if($approved) {

			$query = DB::connection() -> prepare('INSERT INTO Recipe (name, category, instructions, approved, added_by) VALUES (:name, :category, :instructions, true, :added_by) RETURNING id;');

		} else {

			$query = DB::connection() -> prepare('INSERT INTO Recipe (name, category, instructions, approved, added_by) VALUES (:name, :category, :instructions, false, :added_by) RETURNING id;');
		}
		$query -> execute(array('name' => $this->name, 'category' => $category_id, 'instructions' => $this->instructions, 'added_by' => $this->added_by));

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
			$ingredient->addToRecipe($this->id);
		}
	}

	public function delete() {
		$query = DB::connection()->prepare('DELETE FROM Recipe_ingredient WHERE recipe = :id;');
		$query->execute(array('id' => $this->id));

		$query = DB::connection()->prepare('DELETE FROM Recipe WHERE id = :id;');
		$query->execute(array('id' => $this->id));

		Ingredient::deleteAllInactive();
	}

	//divide into smaller pieces
	public static function findAll($options) {
		$parameters = array();

		$query_string = 'SELECT Recipe.id AS id, Recipe.name AS name, Category.name AS category, Ingredients.number_of_ingredients AS number_of_ingredients 
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id 
			LEFT JOIN 
			(SELECT recipe, COUNT(*) AS number_of_ingredients FROM Recipe_ingredient GROUP BY recipe) AS Ingredients 
			ON Ingredients.recipe = Recipe.id 
			WHERE Recipe.approved = true';


		if(isset($options['search'])) {
			$query_string .= ' AND Recipe.name LIKE :like';
			$parameters['like'] = '%' . $options['search'] . '%';
		}

		if(isset($options['categories'])) {
			$first = true;
			$query_string .= ' AND Recipe.category IN (';

			foreach ($options['categories'] as $category) {
				if(!$first) {
					$query_string .= ', ';
				} else {
					$first = false;
				}

				$query_string .= $category;
			}

			$query_string .= ')';
		}

		if(isset($options['ingredients'])) {
			$chosen_numb_ing = count($options['ingredients']);

			$query_string .= ' AND Recipe.id IN (SELECT Recipe FROM (SELECT recipe, COUNT(*) AS chosen_ings_in_recipe FROM Recipe_ingredient WHERE ingredient IN (';

			$first = true;
			foreach ($options['ingredients'] as $chosen_ing) {
				if(!$first) {
					$query_string .= ', ';
				} else {
					$first = false;
				}

				$query_string .= $chosen_ing;
			}

			$query_string .= ') GROUP BY recipe) AS recipes_with_chosen_ing WHERE recipes_with_chosen_ing.chosen_ings_in_recipe = ' . $chosen_numb_ing . ') ';

		}


		$query_string .= ' ORDER BY ';

		if(strcmp($options['order'], 'name') == 0) {
			$query_string .= 'name';

		} else if(strcmp($options['order'], 'category') == 0) {
			$query_string .= 'Category.id';

		} else {
			$query_string .= 'number_of_ingredients';

		}

		$query = DB::connection()->prepare($query_string . ';');
		$query->execute($parameters);

		return self::recipesFromQuery($query);
	}


	public static function findOne($id) {
		$query = DB::connection() -> prepare('SELECT Recipe.id AS id, Recipe.name AS name, Recipe.instructions AS instructions, Category.name AS category, Recipe_ingredient.amount AS amount, Ingredient.name AS ingredient
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id
			LEFT JOIN Recipe_ingredient ON Recipe.id = Recipe_ingredient.recipe
			LEFT JOIN Ingredient ON Recipe_ingredient.ingredient = Ingredient.id
			WHERE Recipe.id = :id;');

		$query -> execute(array('id' => $id));
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
		$query = DB::connection()->prepare('SELECT Recipe.id AS id, Recipe.name AS name, Category.name AS category, Ingredients.number_of_ingredients AS number_of_ingredients 
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id 
			LEFT JOIN 
			(SELECT recipe, COUNT(*) AS number_of_ingredients FROM Recipe_ingredient GROUP BY recipe) AS Ingredients 
			ON Ingredients.recipe = Recipe.id 
			WHERE Recipe.approved = false;');
		$query->execute();

		return self::recipesFromQuery($query);
	}


	private static function recipesFromQuery($query) {

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


	public function approve() {
		$query = DB::connection()->prepare('UPDATE Recipe SET approved = true WHERE id = :id;');
		$query->execute(array('id' => $this->id));
	}

	//when is needed, why not simply when find?
	public static function isApproved($id) {
		$query = DB::connection()->prepare('SELECT approved FROM Recipe WHERE id = :id;');
		$query->execute(array('id' => $id));
		$row = $query->fetch();

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
			WHERE name = :name AND approved = true LIMIT 1;');
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

	public function validate_ingredients() {
		$errors = array();

		if(count($this->ingredients) < 2) {
			$errors[] = 'The drink has to have at least two ingredients';
		}
		if($this->duplicate_ingredients()) {
			$errors[] = "The drink can't have two or more ingredients with the same name";
		}

		return $errors;
	}

	private function duplicate_ingredients() {
		$temp = array();
		foreach ($this->ingredients as $ingredient) {
			if(in_array($ingredient->name, $temp)) {
				return true;

			} else {
				$temp[] = $ingredient->name;
			}
		}

		return false;
	}

}