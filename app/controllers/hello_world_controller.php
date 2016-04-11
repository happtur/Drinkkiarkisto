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
  }
