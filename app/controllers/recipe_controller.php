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
	}

	//"form within a form",google and/or addingredientbutton -> .../edit/addingredient, save -> .../edit. attributes from all, ingredients separately. somehow change edit_drink or have a separate .../edit/temp (because ingredients could have been "deleted")
	public static function update($id) {

	}

	public static function delete($id) {
		$recipe = Recipe::findOne($id);
		$recipe->delete();
		Redirect::to('/', array('success' => 'Drink ' . $recipe->name . ' has been deleted'));
	}

	//create_recipe
	public static function new_recipe_page() {
		View::make('recipes/new_drink.html');
	}

	public static function store_recipe() {
		//ingredients on separate page at first?
		$params = $_POST;

		$attributes = array('name' => $params['name'],
			'category' => $params['category'],
			'instructions' => $params['instructions']);

		$category = Recipe::category($params['category']);

		$drink = new Recipe(array_merge($attributes, array('category' => $category));

		//does not check for category-error since that will be changed to 'choose from'-input
			$errors = $drink->errors();

			if(count($errors) == 0) {
				$drink -> save();

			//temporary solution
				Redirect::to('/drink/addingredient/' . $drink->id, array('success' => 'Nearly there :) just add some ingredients'));
			} else {
				Redirect::to('/drink/new', array('errors' => $errors, 'attributes' => $attributes));
			}
	}

	//add_ingredient
	public static function add_ingredient_page($id) {
		//one should be able to select from ingredients already in the database
		//as well as adding new ones.
		$id = (int) $id;
		$recipe = Recipe::findOne($id);

		View::make('recipes/add_ingredient.html', array('recipe' => $recipe));
	}

	public static function store_ingredient($id) {
		$params = $_POST;
		$attributes = array('name' => $params['name'], 'amount' => $params['amount']);
		$ingredient = new Ingredient($attributes);

		$errors = $ingredient->errors();

		if(count($errors) == 0) {

			$recipe = Recipe::findOne($id);
			$recipe->addIngredient($ingredient);


			Redirect::to('/drink/addingredient/' . $recipe->id, array('success' => 'Ingredient added'));

		} else {
			Redirect::to('/drink/addIngredient/'$id, array('errors' => $errors, 'attributes' => $attributes));
		}
	}
}