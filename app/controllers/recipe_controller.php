<?php

class RecipeController extends BaseController {

	//make search case insensitive?
	public static function list_all() {
		$params = $_GET;

		if(empty($params)) {
			$params['order'] = 'name';
		}

		$recipes = Recipe::findAll($params);
		$ingredients = Ingredient::all(true);
		$categories = Category::all();

		View::make('recipes/list.html', array('recipes' => $recipes, 'ingredients' => $ingredients, 'categories' => $categories, 'chosen' => $params));
	}

	//now anyone can see suggestions here, limit to the suggester? atm: Recipe::isApproved($id)
	public static function show($id) {
		$recipe = Recipe::findOne($id);
		View::make('recipes/drink.html', array('recipe' => $recipe));
	}

	public static function edit($id) {
		self::check_logged_in_is_admin();

		$recipe = Recipe::findOne($id);
		$categories = Category::all();

		View::make('recipes/drink_form.html', array('type' => 'edit_drink', 'recipe' => $recipe, 'categories' => $categories));
	}


	public static function update($id) {
		self::check_logged_in_is_admin();

		$params = $_POST;
		$params['id'] = $id;

		if($params['action'] == 'add') {
			self::add_ingredient($params, 'edit_drink');
			
		} else if($params['action'] == 'save') {
			$recipe = self::get_recipe($params);
			$errors = $recipe->errors();

			if(count($errors) == 0) {
				$recipe->update();

				if(Recipe::isApproved($id)) { 
					Redirect::to('/drink/' . $id, array('success' => 'The changes were saved'));

				} else {
					Redirect::to('/drink/suggestion/' . $id, array('success' => 'The changes were saved, but the suggested drink has not yet been approved'));
				}				

			} else {
				$categories = Category::all();

				View::make('recipes/drink_form.html', array('type' => 'edit_drink', 'recipe' => $recipe, 'errors' => $errors, 'categories' => $categories));
			}
		}

	}

	public static function delete($id) {
		$recipe_name = self::remove_from_db($id);
		Redirect::to('/', array('success' => 'Drink ' . $recipe_name . ' has been deleted'));
	}


	public static function new_drink() {
		self::check_logged_in_is_admin();

		$categories = Category::all();
		View::make('recipes/drink_form.html', array('type' => 'new_drink', 'categories' => $categories));
	}
 

	public static function store() {
		self::check_logged_in_is_admin();

		$params = $_POST;

		if($params['action'] == "add") {
			self::add_ingredient($params, 'new_drink');

		} else if($params['action'] == "save") {

			$params['added_by'] = self::get_user_logged_in()->id;
			$recipe = self::get_recipe($params);
			$errors = $recipe->errors();

			if(count($errors) == 0) {
				$recipe->save(true);
				Redirect::to('/drink/' . $recipe->id, array('success' => 'New drink, ' . $recipe->name . ', successfully added'));

			} else {
				$categories = Category::all();
				View::make('recipes/drink_form.html', array('type' => 'new_drink','recipe' => $recipe, 'errors' => $errors, 'categories' => $categories));
			}
		}

	}

	protected static function remove_from_db($id) {
		self::check_logged_in_is_admin();

		$recipe = Recipe::findOne($id);
		$name = $recipe->name;
		$recipe->delete();

		return $name;
	}

	protected static function old_ingredients($params) {
		$ingredients = array();
		$max_ing = (count($params) - 4) / 2;

		for($i = 1; $i <= $max_ing; $i++) {
			$key_name = 'ing_name' . $i;

			if(isset($params[$key_name])) {
				$ing_name = $params[$key_name];

				$key_amount = 'ing_amount' . $i;		
				$ing_amount = $params[$key_amount];

				$ingredients[] = new Ingredient(array('name' => $ing_name, 'amount' => $ing_amount));
			}
		}

		return $ingredients;
	}

	protected static function add_ingredient($params, $type) {
		$categories = Category::all();
		$ingredients = $ingredients = self::old_ingredients($params);

		$newIngredient = new Ingredient(array('name' => $params['new_ingredient_name'], 'amount' => $params['new_ingredient_amount']));
		$errors = $newIngredient->errors();

		$for_view = array('type' => $type, 'errors' => $errors, 'categories' => $categories);

		if(count($errors) == 0) {
			$ingredients[] = $newIngredient;			
		} else {
			//fix this in drink_form
			$for_view['new_ingredient_name'] = $newIngredient->name;
			$for_view['new_ingredient_amount'] = $newIngredient->amount;
		}


		$for_recipe = array(
			'name' => $params['name'],
			'category' => $params['category'],
			'instructions' => $params['instructions'],
			'ingredients' => $ingredients);

		if(isset($params['id'])) {
			$for_recipe['id'] = $params['id'];
		}


		$for_view['recipe'] = new Recipe($for_recipe);
		View::make('recipes/drink_form.html', $for_view);
	}

	protected static function get_recipe($params) {
		$ingredients = self::old_ingredients($params);

		$for_recipe = array(
			'name' => $params['name'],
			'category' => $params['category'],
			'instructions' => $params['instructions'],
			'ingredients' => $ingredients);

		if(isset($params['id'])) {
			$for_recipe['id'] = $params['id'];
		}

		if(isset($params['added_by'])) {
			$for_recipe['added_by'] = $params['added_by'];
		}

		return new Recipe($for_recipe);		
	}

}