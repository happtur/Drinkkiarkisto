<?php


class Recipe extends BaseModel {
	public $id, $name, $category, $instructions, $added_by, $ingredients, $numberOfIngredients, $validators;

	public function __construct($attributes) {
		parent::__construct($attributes);

		$this->validators = array('validate_name', 'validate_instructions');
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
			$ingredient->saveIfNeeded();

			//move to ingredient? $ingredient->addToRecipe($recipe_id)
			$query = DB::connection()->prepare('INSERT INTO Recipe_ingredient (recipe, ingredient, amount) VALUES (:recipe, :ingredient, :amount);');
			$query->execute(array('recipe' => $this->id, 'ingredient' => $ingredient->id, 'amount' => $ingredient->amount));
		}
	}

	public function delete() {
		$query = DB::connection()->prepare('DELETE FROM Recipe_ingredient WHERE recipe = :id;');
		$query->execute(array('id' => $this->id));

		$query = DB::connection()->prepare('DELETE FROM Recipe WHERE id = :id;');
		$query->execute(array('id' => $this->id));
	}


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

		//as it is now, there will 'always' be an $option['categories']
		//and the most common (all categories) causes the most (and unnecessary) work. fix this :)
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
			// :S ridikilöös (if this, remember to move to the right place)
			// select recipe.id as id, recipe.name as name, category.name as category, ingredients.number_of_ingredients as number_of_ingredients
			// from recipe
			// left join category on recipe.category = category.id
			// left join
			// (select recipe, count(*) as number_of_ingredients
				// from recipe_ingredient
				// group by recipe) as ingredients
				// on ingredients.recipe = recipe.id

			// inner join
			// (select recipe
				// from recipe_ingredient where ingredient = x) as ingredient1
				// on ingredient1.recipe = recipe.id
			// inner join
				// (select recipe
				// from recipe_ingredient where ingredient = y) as ingredient2
				// on ingredient2.recipe = recipe.id
			// inner join
				// (select recipe
				// from recipe_ingredient where ingredient = z) as ingredient3
				// on ingredient3.recipe = recipe.id

			//  where recipe.approved = true
			// and recipe.category in (1,2,3); 
		}

		$query_string .= ' ORDER BY ';

		if(strcmp($options['order'], 'name') == 0) {
			$query_string .= 'name';

		} else if(strcmp($options['order'], 'category') == 0) {
			$query_string .= 'category';

			//0 -> null -> last
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

}