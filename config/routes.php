<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });

  $routes->get('/drinks', function() {
  	RecipeController::list_drinks();
  });

  $routes->get('/drink/:id', function($id) {
  	RecipeController::show_drink($id);
  });

  $routes->get('/drink/:id/edit', function($id) {
  	HelloWorldController::edit_drink($id);
  });

