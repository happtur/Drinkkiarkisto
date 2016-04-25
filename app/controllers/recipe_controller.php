<?php

//validate instructions doesn't work when edit, new, suggest

class RecipeController extends BaseController {

	//going to fix the copy paste eventually

	public static function list_drinks() {
		$recipes = Recipe::findAll();
		View::make('recipes/list.html', array('recipes' => $recipes));
	}

	public static function show_drink($id) {
		$recipe = Recipe::findOne($id);
		View::make('recipes/drink.html', array('recipe' => $recipe));
	}

	public static function edit_drink($id) {
			self::check_logged_in_is_admin();

			$recipe = Recipe::findOne($id);
			$categories = Category::all();

			//View::make('recipes/drink_form.html', array('recipe' => $recipe, 'type' => "edit"));
			View::make('recipes/edit_drink.html', array('recipe' => $recipe, 'categories' => $categories));
	}


	public static function update($id) {
		self::check_logged_in_is_admin();

		$params = $_POST;

		$categories = Category::all();

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

		if($params['action'] == 'add') {

			$newIngredient = new Ingredient(array('name' => $params['new_ingredient_name'], 'amount' => $params['new_ingredient_amount']));

			$errors = $newIngredient->errors();

			if(count($errors) == 0) {
				$ingredients[] = $newIngredient;

				$recipe = new Recipe(array(
					'id' => $id,
					'name' => $params['name'],
					'category' => $params['category'],
					'instructions' => $params['instructions'],
					'ingredients' => $ingredients));

				View::make('recipes/edit_drink.html', array('recipe' => $recipe, 'categories' => $categories));

			//change so the boxes display the attempted input-values
			} else {
				$recipe = new Recipe(array(
					'id' => $id,
					'name' => $params['name'],
					'category' => $params['category'],
					'instructions' => $params['instructions'],
					'ingredients' => $ingredients));

				View::make('recipes/edit_drink.html', array('recipe' => $recipe, 'errors' => $errors, 'categories' => $categories));
			}
			
		//else?
		} else if($params['action'] == 'save') {
			$recipe = new Recipe(array(
				'id' => $id,
				'name' => $params['name'],
				'category' => $params['category'],
				'instructions' => $params['instructions'],
				'ingredients' => $ingredients));

			$errors = $recipe->errors();

			if(count($errors) == 0) {
				$recipe->update();

				if(Recipe::isApproved($id)) { 
					Redirect::to('/drink/' . $id, array('success' => 'The changes were saved'));

				} else {
					Redirect::to('/drink/suggestion/' . $id, array('success' => 'The changes were saved, but the suggested drink has not yet been approved'));
				}

				

			} else {
				View::make('recipes/edit_drink.html', array('recipe' => $recipe, 'errors' => $errors, 'categories' => $categories));
			}
		}

		//check if admin
		//save checkbox ingredients
		//which action?
		//addIng:
			//validateIng
				//no errors
					//add new ingredient to ingredients
					//save attributes/recipe with original id
					//display same page with 'recipe' => attributes including new ingredient
				//error (including empty inputboxes)
					//same page with error-alert, 'recipe' => attributes, not the new one (or keep it in the add-boxes?)
		//save:
			//create recipe-object with original id!, validate (->errors())
				//no errors
					//update recipe in database as approved
					//load new drink's page
				//error
					//same page with error-alert, 'recipe' => attributes

	}

	public static function delete($id) {
		self::check_logged_in_is_admin();

		$recipe = Recipe::findOne($id);
		$approved = Recipe::isApproved($id);
		$recipe->delete();

		if($approved) {
			Redirect::to('/', array('success' => 'Drink ' . $recipe->name . ' has been deleted'));
		} else {
			Redirect::to('/', array('success' => 'Suggestion ' . $recipe->name . ' was dismissed'));
		}
	}


	public static function new_recipe_page() {
		self::check_logged_in_is_admin();

		//View::make('recipes/drink_form.html', array('type' => "new"));
		View::make('recipes/new_drink.html');
	}

	//does not check for category-error since that will be changed to 'choose from'-input
	public static function store_recipe() {
		self::check_logged_in_is_admin();

		$params = $_POST;

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

		if($params['action'] == "add") {

			$newIngredient = new Ingredient(array('name' => $params['new_ingredient_name'], 'amount' => $params['new_ingredient_amount']));

			$errors = $newIngredient->errors();

			if(count($errors) == 0) {
				$ingredients[] = $newIngredient;

				$recipe = new Recipe(array(
					'name' => $params['name'],
					'category' => $params['category'],
					'instructions' => $params['instructions'],
					'ingredients' => $ingredients));

				View::make('recipes/new_drink.html', array('recipe' => $recipe));

				//change so it keeps the faulty input in the boxes.
			} else {
				$recipe = new Recipe(array(
					'name' => $params['name'],
					'category' => $params['category'],
					'instructions' => $params['instructions'],
					'ingredients' => $ingredients));

				View::make('recipes/new_drink.html', array('recipe' => $recipe, 'errors' => $errors));
			}

		//else?
		} else if($params['action'] == "save") {

			$recipe = new Recipe(array(
				'name' => $params['name'],
				'category' => $params['category'],
				'instructions' => $params['instructions'],
				'ingredients' => $ingredients,
				'added_by' => self::get_user_logged_in()->id));

			$errors = $recipe->errors();

			if(count($errors) == 0) {
				$recipe->save(true);
				Redirect::to('/drink/' . $recipe->id, array('success' => 'New drink, ' . $recipe->name . ', successfully added'));

			} else {
				View::make('recipes/new_drink.html', array('recipe' => $recipe, 'errors' => $errors));
			}
		}

		//check if admin
		//save checkbox ingredients
		//which action?
		//addIng:
			//validateIng
				//no errors
					//add new ingredient to ingredients
					//save attributes/recipe
					//display same page with 'recipe' => attributes including new ingredient
				//error (including empty boxes)
					//same page with error-alert, 'recipe' => attributes, not the new one (or keep it in the add-boxes?)
		//save:
			//create recipe-object (incl. user), validate (->errors())
				//no errors
					//save recipe in database AS APPROVED
					//load new drink's page
				//error
					//same page with error-alert, 'recipe' => attributes

	}




	//move these into suggestionController?

	public static function suggestNew() {
		//View::make('recipes/drink_form.html', array('type' => "suggest_new"));
		View::make('recipes/suggest_drink.html');
	}
	
	public static function saveSuggestion() {

		$params = $_POST;

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

		if($params['action'] == "add") {

			$newIngredient = new Ingredient(array('name' => $params['new_ingredient_name'], 'amount' => $params['new_ingredient_amount']));

			$errors = $newIngredient->errors();

			if(count($errors) == 0) {
				$ingredients[] = $newIngredient;

				$recipe = new Recipe(array(
					'name' => $params['name'],
					'category' => $params['category'],
					'instructions' => $params['instructions'],
					'ingredients' => $ingredients));

				View::make('recipes/suggest_drink.html', array('recipe' => $recipe));

				//change so it keeps the faulty input in the boxes.
			} else {
				$recipe = new Recipe(array(
					'name' => $params['name'],
					'category' => $params['category'],
					'instructions' => $params['instructions'],
					'ingredients' => $ingredients));

				View::make('recipes/suggest_drink.html', array('recipe' => $recipe, 'errors' => $errors));
			}

		//else?
		} else if($params['action'] == "save") {

			$attributes = array(
				'name' => $params['name'],
				'category' => $params['category'],
				'instructions' => $params['instructions'],
				'ingredients' => $ingredients);

			$user = self::get_user_logged_in();
			if($user) {
				$attributes['added_by'] = $user->id;
			}

			$recipe = new Recipe($attributes);
			$errors = $recipe->errors();

			if(count($errors) == 0) {
				$recipe->save(false);
				Redirect::to('/drink/' . $recipe->id, array('success' => 'New drink suggestion, ' . $recipe->name . ', sent'));

			} else {
				View::make('recipes/suggest_drink.html', array('recipe' => $recipe, 'errors' => $errors));
			}
		}

		//save checkbox ingredients
		//which action?
		//addIng:
			//validateIng
				//no errors
					//add new ingredient to ingredients
					//save attributes/recipe with original id
					//display same page with 'recipe' => attributes including new ingredient
				//error (including empty inputboxes)
					//same page with error-alert, 'recipe' => attributes, not the new one (or keep it in the add-boxes?)
		//save:
			//create recipe-object (incl. user, if there is one), validate (->errors())
				//no errors
					//save recipe in database as not approved
					//load new drink's page
				//error
					//same page with error-alert, 'recipe' => attributes

	}

	//view not editable, has buttons:
	//approve, dismiss, edit
	//edit: save ---> approved OR **save ---> reload viewSuggestion with changes**
	public static function viewSuggestion($id) {
		self::check_logged_in_is_admin();

		$recipe = Recipe::findOne($id);
		View::make('/recipes/suggestion.html', array('recipe' => $recipe));
	}


	public static function approveSuggestion($id) {
		self::check_logged_in_is_admin();

		Recipe::approve($id);
		Redirect::to('/drink/' . $id, array('success' => 'Drink approved'));
	}


	public static function suggestions() {
		self::check_logged_in_is_admin();

		$suggestions = Recipe::suggestions();
		View::make('/recipes/list_suggestions.html', array('recipes' => $suggestions));
		
		//View suggestionlist (like list, without delete, without newbutton and with namelink---> viewsuggestion)
			//should it show who added it? should list? then I would have to change recipe->user to User instead of int.
	}

}