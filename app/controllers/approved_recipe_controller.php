<?php

class ApprovedRecipeController extends RecipeController {

	public static function all() {
		$params = $_GET;

		if(empty($params)) {
			$params['order'] = 'name';
		}

		$recipes = ApprovedRecipe::all($params);
		//Ingredient::all($only_ingredients_used_in_approved_recipes);
		$ingredients = Ingredient::all(true);
		$categories = Category::all();

		View::make('recipes/list_approved.html', array('recipes' => $recipes, 'ingredients' => $ingredients, 'categories' => $categories, 'chosen' => $params));
	}

	public static function new_drink() {
		self::check_logged_in_is_admin();

		$categories = Category::all();
		View::make('recipes/drink_form.html', array('type' => 'new_drink', 'categories' => $categories));
	}

	public static function save() {
		self::check_logged_in_is_admin();

		$params = $_POST;

		if($params['action'] == "add") {
			self::add_ingredient($params, 'new_drink');

		} else if($params['action'] == "save") {

			$params['added_by'] = self::get_user_logged_in()->id;
			$recipe = new ApprovedRecipe(self::get_recipe($params));
			$errors = $recipe->errors();

			if(count($errors) == 0) {
				$recipe->save();
				Redirect::to('/drink/' . $recipe->id, array('success' => 'New drink, ' . $recipe->name . ', successfully added'));

			} else {
				$categories = Category::all();
				View::make('recipes/drink_form.html', array('type' => 'new_drink','recipe' => $recipe, 'errors' => $errors, 'categories' => $categories));
			}
		}
	}
}