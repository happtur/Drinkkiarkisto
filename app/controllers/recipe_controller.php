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

	
	public static function edit_drink($id, $already_changed) {
		if(!$already_changed) {
			$recipe = Recipe::findOne($id);
			View::make('recipes/edit_drink.html', array('recipe' => $recipe));
		}

		View::make('recipes/edit_drink.html');
	}

	//"form within a form",google and/or addingredientbutton -> .../edit/addingredient, save -> .../edit. attributes from all, ingredients separately. somehow change edit_drink or have a separate .../edit/temp (because ingredients could have been "deleted")
	//messy, messy, messy
	//everything lost if doesn't use add or save
	public static function update($id) {
		$params = $_POST;

		$ingredients = array();
		$max_ing = (count($params) - 4) / 2;

		for($i = 1; $i <= $max_ing; $i++) {
			$si = 'ingr' . $i;

			if(isset($params[$si])) {
				$ing_name = $params[$si];
				//bad name
				$identifier = 'id' . Ingredient::getId($ing_name);
				$ing_amount = $params[$identifier];
				$ingredients[] = new Ingredient(array('name' => $ing_name, 'amount' => $ing_amount));
			}
		}

		if($params['action'] == 'add') {
			if(!empty($params['new_ingredient_name']) && !empty($params['new_ingredient_amount'])) {

				$ingredients[] = new Ingredient(array('name' => $params['new_ingredient_name'], 'amount' => $params['new_ingredient_amount']));
			}
			$recipe = new Recipe(array(
				'id' => $id,
				'name' => $params['name'],
				'category' => $params['category'],
				'instructions' => $params['instructions'],
				'ingredients' => $ingredients));

			Redirect::to('/drink/' . $id . '/edit/temp', array('recipe' => $recipe));
		} else {
			$recipe = new Recipe(array(
				'id' => $id,
				'name' => $params['name'],
				'category' => $params['category'],
				'instructions' => $params['instructions'],
				'ingredients' => $ingredients));

			$recipe->update();

			Redirect::to('/drink/' . $id, array('recipe' => $recipe, 'success' => 'Your changes were saved'));
		}

		Kint::dump($params);
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

		$drink = new Recipe($attributes);

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
			Redirect::to('/drink/addingredient/' . $id, array('errors' => $errors, 'attributes' => $attributes));
		}
	}
}