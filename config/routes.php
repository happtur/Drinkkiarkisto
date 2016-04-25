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

$routes->get('/user/:id', function($id) {
  UserController::show_user($id);
});

$routes->get('/users', function() {
  UserController::list_all();
});

$routes->get('/user/new', function() {
  UserController::new_user();
});

$routes->post('/user/new', function() {
  UserController::handle_new_user();
});

$routes->post('/user/:id/password', function($id) {
  UserController::change_password($id);
});

$routes->get('/user/:id/delete', function($id) {
  UserController::delete($id);
});

$routes->get('/user/:id/makeadmin', function($id) {
  UserController::make_admin($id);
});

$routes->get('/user/:id', function($id) {
  UserController::show_user($id);
});

$routes->get('/drinks', function() {
 RecipeController::list_drinks();
});

//change all /drink/suggestion/... to /suggestion/...?
$routes->get('/drink/suggestions', function() {
  RecipeController::suggestions();
});

$routes->get('/drink/suggestion/:id/delete', function($id) {
  RecipeController::delete($id);
});

$routes->get('/drink/suggestion/new', function() {
  RecipeController::suggestNew();
});

$routes->post('/drink/suggestion/new', function() {
  RecipeController::saveSuggestion();
});

$routes->get('/drink/suggestion/:id/approve', function($id) {
  RecipeController::approveSuggestion($id);
});

$routes->get('/drink/suggestion/:id', function($id) {
  RecipeController::viewSuggestion($id);
});

$routes->get('/drink/new', function() {
  RecipeController::new_recipe_page();
});

$routes->post('/drink/new', function() {
  RecipeController::store_recipe();
});

$routes->get('/drink/:id/edit', function($id) {
  RecipeController::edit_drink($id);
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


