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

$routes->get('/drink/new', function() {
  RecipeController::create();
});

$routes->post('/drink/new', function() {
  RecipeController::store();
});

$routes->get('/drink/addingredient/:id', function($id) {
  RecipeController::addIngredientsPage($id);
});

$routes->post('/drink/addingredient/:id', function($id) {
  RecipeController::storeIngredient($id);
});

$routes->get('/drink/:id/edit', function($id) {
  RecipeController::edit_drink($id);
});

$routes->get('/drink/:id', function($id) {
 RecipeController::show_drink($id);
});


