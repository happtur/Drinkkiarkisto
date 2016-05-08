<?php

class SuggestionController extends RecipeController {

	public static function new_suggestion() {
		$categories = Category::all();
		View::make('recipes/drink_form.html', array('type' => 'suggest_drink','categories' => $categories));
	}

	public static function save() {
		$params = $_POST;

		if($params['action'] == "add") {
			self::add_ingredient($params, 'suggest_drink');

		} else if($params['action'] == "save") {

			$user = self::get_user_logged_in();
			if($user) {
				$params['added_by'] = $user->id;
			}

			$recipe = new Suggestion(self::get_recipe($params));
			$errors = $recipe->errors();


			if(count($errors) == 0) {
				$recipe->save();
				Redirect::to('/drink/' . $recipe->id, array('success' => 'New drink suggestion, ' . $recipe->name . ', sent'));

			} else {
				$categories = Category::all();
				View::make('recipes/drink_form.html', array('type' => 'suggest_drink','recipe' => $recipe, 'errors' => $errors, 'categories' => $categories));
			}
		}

	}


	public static function show($id) {
		self::check_logged_in_is_admin();

		$recipe = Recipe::find_one($id);
		View::make('/recipes/show.html', array('type' => 'suggestion', 'recipe' => $recipe));
	}

	public static function approve($id) {
		self::check_logged_in_is_admin();

		$recipe = Recipe::find_one($id);
		$errors = $recipe->errors();

		if(count($errors) == 0) {
			$recipe->approve();
			Redirect::to('/drink/' . $id, array('success' => 'Drink approved'));
		} else {
			View::make('/recipes/show.html', array('type' => 'suggestion', 'recipe' => $recipe, 'errors' => $errors));
		}
	}


	public static function all() {
		self::check_logged_in_is_admin();

		$suggestions = Suggestion::all();
		View::make('/recipes/list_suggestions.html', array('recipes' => $suggestions));
	}

}