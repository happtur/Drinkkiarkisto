<?php

//fix helloworld :D
$routes->get('/', function() {
  HelloWorldController::index();
});

$routes->get('/hiekkalaatikko', function() {
  HelloWorldController::sandbox();
});

$routes->get('/login', function() {
  UserController::login();
});

$routes->post('/login', function() {
  UserController::handle_login();
});

$routes->post('/logout', function() {
  UserController::logout();
});

$routes->get('/drinks', function() {
 RecipeController::list_drinks();
});

$routes->get('/drink/new', function() {
  RecipeController::new_recipe_page();
});

$routes->post('/drink/new', function() {
  RecipeController::store_recipe();
});

$routes->get('/drink/addingredient/:id', function($id) {
  RecipeController::add_ingredient_page($id);
});

$routes->post('/drink/addingredient/:id', function($id) {
  RecipeController::store_ingredient($id);
});

$routes->get('/drink/:id/edit/temp', function($id) {
  RecipeController::edit_drink($id, true);
});

$routes->get('/drink/:id/edit', function($id) {
  RecipeController::edit_drink($id, false);
});

$routes->post('/drink/:id/edit', function($id) {
  RecipeController::update($id);
});

$routes->get('/drink/:id/delete', function($id) {
	RecipeController::delete($id);
});

$routes->get('/drink/:id', function($id) {
 RecipeController::show_drink($id);
});


