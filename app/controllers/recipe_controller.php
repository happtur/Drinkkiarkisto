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
}