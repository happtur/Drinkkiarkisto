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

			$recipe = self::get_recipe($params);
			$errors = $recipe->errors();


			if(count($errors) == 0) {
				$recipe->save(false);
				Redirect::to('/drink/' . $recipe->id, array('success' => 'New drink suggestion, ' . $recipe->name . ', sent'));

			} else {
				$categories = Category::all();
				View::make('recipes/drink_form.html', array('type' => 'suggest_drink','recipe' => $recipe, 'errors' => $errors, 'categories' => $categories));
			}
		}

	}


	public static function view($id) {
		self::check_logged_in_is_admin();

		$recipe = Recipe::findOne($id);
		View::make('/recipes/suggestion.html', array('recipe' => $recipe));
	}

	public static function approve($id) {
		self::check_logged_in_is_admin();

		$recipe = Recipe::findOne($id);
		$errors = $recipe->errors();

		if(count($errors) == 0) {
			$recipe->approve();
			Redirect::to('/drink/' . $id, array('success' => 'Drink approved'));
		} else {
			View::make('/recipes/suggestion.html', array('recipe' => $recipe, 'errors' => $errors));
		}
	}


	public static function list_all() {
		self::check_logged_in_is_admin();

		$suggestions = Recipe::suggestions();
		View::make('/recipes/list_suggestions.html', array('recipes' => $suggestions));
	}

	public static function delete($id) {
		$recipe_name = self::remove_from_db($id);
		//redirect to suggestionlist?
		Redirect::to('/', array('success' => 'Suggestion ' . $recipe_name . ' was dismissed'));
	}

}