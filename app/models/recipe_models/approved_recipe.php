<?php

class ApprovedRecipe extends Recipe {

	public function save() {
		$query = DB::connection() -> prepare('INSERT INTO Recipe (name, category, instructions, approved, added_by) VALUES (:name, :category, :instructions, true, :added_by) RETURNING id;');

		$this->execute_save_query($query);
	}

	public static function all($options) {
		$parameters = array();

		$query_string = 'SELECT Recipe.id AS id, Recipe.name AS name, Category.name AS category, Ingredients.number_of_ingredients AS number_of_ingredients 
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id 
			LEFT JOIN 
			(SELECT recipe, COUNT(*) AS number_of_ingredients FROM Recipe_ingredient GROUP BY recipe) AS Ingredients 
			ON Ingredients.recipe = Recipe.id 
			WHERE Recipe.approved = true';

		//not sure if should or shouldn't create index? it doesn't seem to use it (according to my interpretation of EXPLAIN 'feedback' XD)  well of course not with a db of this size... buut, when bigger?
		if(isset($options['search'])) {
			$query_string .= ' AND LOWER(Recipe.name) LIKE LOWER(:like)';
			$parameters['like'] = '%' . $options['search'] . '%';
		}

		if(isset($options['categories'])) {
			$query_string .= self::string_for_search_on_categories($options['categories']);
		}

		if(isset($options['ingredients'])) {
			$query_string .= self::string_for_search_on_ingredients($options['ingredients']);
		}

		$query_string .= self::string_for_order_by($options['order']);

		$query = DB::connection()->prepare($query_string . ';');
		$query->execute($parameters);

		return self::recipes_from_query($query);
	}

	private static function string_for_search_on_categories($categories) {
		$first = true;
		$sql_string = ' AND Recipe.category IN (';

		foreach ($categories as $category) {
			if(!$first) {
				$sql_string .= ', ';
			} else {
				$first = false;
			}

			$sql_string .= $category;
		}

		$sql_string .= ')';
		return $sql_string;
	}

	private static function string_for_search_on_ingredients($ingredients) {
		$chosen_numb_ing = count($ingredients);

		$sql_string = ' AND Recipe.id IN (SELECT recipe FROM (SELECT recipe, COUNT(*) AS chosen_ings_in_recipe FROM Recipe_ingredient WHERE ingredient IN (';

		$first = true;
		foreach ($ingredients as $chosen_ing) {
			if(!$first) {
				$sql_string .= ', ';
			} else {
				$first = false;
			}

			$sql_string .= $chosen_ing;
		}

		$sql_string .= ') GROUP BY recipe) AS recipes_with_chosen_ing WHERE recipes_with_chosen_ing.chosen_ings_in_recipe = ' . $chosen_numb_ing . ') ';
		return $sql_string;
	}

	private static function string_for_order_by($order) {
		$sql_string = ' ORDER BY ';

		if(strcmp($order, 'name') == 0) {
			$sql_string .= 'name';

		} else if(strcmp($order, 'category') == 0) {
			$sql_string .= 'Category.id';

		} else {
			$sql_string .= 'number_of_ingredients';

		}

		return $sql_string;
	}
}