<?php

class Suggestion extends Recipe {

	public function save() {
		$query = DB::connection() -> prepare('INSERT INTO Recipe (name, category, instructions, approved, added_by) VALUES (:name, :category, :instructions, false, :added_by) RETURNING id;');

		$this->execute_save_query($query);
	}

	public static function all() {
		$query = DB::connection()->prepare('SELECT Recipe.id AS id, Recipe.name AS name, Category.name AS category, Ingredients.number_of_ingredients AS number_of_ingredients 
			FROM Recipe 
			LEFT JOIN Category ON Recipe.category = Category.id 
			LEFT JOIN 
			(SELECT recipe, COUNT(*) AS number_of_ingredients FROM Recipe_ingredient GROUP BY recipe) AS Ingredients 
			ON Ingredients.recipe = Recipe.id 
			WHERE Recipe.approved = false;');
		$query->execute();

		return self::recipes_from_query($query);
	}

}