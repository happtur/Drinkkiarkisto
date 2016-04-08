<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  View::make('home.html');
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      //View::make('helloworld.html');
      //View::make('esimerkki.html');
      $recipes = Recipe::findAll();
      $recipe = Recipe::findOne(1);

      Kint::dump($recipes);
      Kint::dump($recipe);
    }

    public static function list_drinks() {
      View::make('plans/drink_list_page.html');
    }

    public static function show_drink() {
      View::make('plans/drink_page.html');
    }

    public static function edit_drink() {
      View::make('plans/edit_drink_page.html');
    }
  }
