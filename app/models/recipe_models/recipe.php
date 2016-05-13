<?php

//category is category_name (string)
//added_by is user_id (int)
//ingredients is array of Ingredients
class Recipe extends BaseModel {
	public $id, $name, $category, $instructions, $added_by, $ingredients, $number_of_ingredients, $validators;

	public function __construct($attributes) {
		parent::__construct($attributes);

		$this->validators = array('validate_name', 'validate_instructions', 'validate_ingredients');
	}

	public function update() {
		$query = DB::connection()->prepare('DELETE FROM Recipe_ingredient WHERE recipe = :id;');
		$query->execute(array('id' => $this->id));

		$category = Category::get_id($this->category);

		$query = DB::connection()->prepare('UPDATE Recipe SET name = :name, category = :category, instructions = :instructions WHERE id = :id;');
		$query->execute(array('name' => $this->name, 'category' => $category, 'instructions' => $this->instructions, 'id' => $this->id));

		$this->save_ingredients();
	}

	public function delete() {
		$query = DB::connection()->prepare('DELETE FROM Recipe_ingredient WHERE recipe = :id;');
		$query->execute(array('id' => $this->id));

		$query = DB::connection()->prepare('DELETE FROM Recipe WHERE id = :id;');
		$query->execute(array('id' => $this->id));

		Ingredient::delete_all_inactive();
	}

	public static function find_one($id) {
		$query = DB::connection() -> prepare('SELECT Recipe.id AS id, Recipe.name AS name, Recipe.instructions AS instructions, Category.name AS category, Recipe_ingredient.amount AS amount, Ingredient.name AS ingredient
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id
			LEFT JOIN Recipe_ingredient ON Recipe.id = Recipe_ingredient.recipe
			LEFT JOIN Ingredient ON Recipe_ingredient.ingredient = Ingredient.id
			WHERE Recipe.id = :id;');

		$query -> execute(array('id' => $id));

		return self::recipe_from_query($query);
	}

	public function approve() {
		$query = DB::connection()->prepare('UPDATE Recipe SET approved = true WHERE id = :id;');
		$query->execute(array('id' => $this->id));
	}

	public static function is_approved($id) {
		$query = DB::connection()->prepare('SELECT approved FROM Recipe WHERE id = :id;');
		$query->execute(array('id' => $id));
		$row = $query->fetch();

		if($row and $row['approved'] == 't') {
			return true;
		}

		return false;
	}

	protected function execute_save_query($query) {
		$category = Category::get_id($this->category);

		$query -> execute(array('name' => $this->name, 'category' => $category, 'instructions' => $this->instructions, 'added_by' => $this->added_by));

		$row = $query -> fetch();
		$this->id = $row['id'];

		$this->save_ingredients();
	}

	protected static function recipe_from_query($query) {
		$row = $query->fetch();
		$ingredients = array();

		$id = $row['id'];
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

	protected static function recipes_from_query($query) {
		$rows = $query -> fetchAll();
		$recipes = array();

		foreach($rows as $row) {
			$recipes[] = new Recipe(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'category' => $row['category'],
				'number_of_ingredients' => $row['number_of_ingredients']));
		}

		return $recipes;
	}

	protected function save_ingredients() {
		foreach ($this->ingredients as $ingredient) {
			$ingredient->add_to_recipe($this->id);
		}
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
			WHERE LOWER(name) = LOWER(:name) AND approved = true LIMIT 1;');
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