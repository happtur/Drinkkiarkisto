<?php

class RecipeController extends BaseController {

	public static function list_drinks() {

		$recipes = Recipe::findAll();

		View::make('recipes/list.html', array('recipes' => $recipes));
	}

	public static function show_drink($id) {

		$recipe = Recipe::findOne($id);

		View::make('recipes/drink.html', array('recipe' => $recipe));

	}

	public static function edit_drink($id) {

		$recipe = Recipe::findOne($id);

		View::make('recipes/edit_drink.html', array('recipe' => $recipe));
		Kint::dump($recipe);
	}

	public static function store() {
		//ingredients on separate page at first?
		$params = $_POST;

		$category = Recipe::category($params['category']);

		$drink = new Recipe(array('name' => $params['name'], 'category' => $category, 'instructions' => $params['instructions']));

		$drink -> save();

		//temporary solution
		Redirect::to('/drink/addingredient/' . $drink->id, array('message' => 'Nearly there :) just add some ingredients'));
	}

	public static function create() {
		View::make('recipes/new_drink.html');
	}

	public static function addIngredientsPage($id) {
		//one should be able to select from ingredients already in the database
		//as well as adding new ones.
		$id = (int) $id;
		$recipe = Recipe::findOne($id);

		View::make('recipes/add_ingredient.html', array('recipe' => $recipe));
	}

	public static function storeIngredient($id) {
		$params = $_POST;
		$ingredient = new Ingredient(array('name' => $params['name'], 'amount' => $params['amount']));

		$recipe = Recipe::findOne($id);
		$recipe->addIngredient($ingredient);

		//check Redirect::to --- message
		Redirect::to('/drink/addingredient/' . $recipe->id, array('message' => 'Ingredient added'));
	}
}