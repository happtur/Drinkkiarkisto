<?php

$routes->get('/', function() {
  DrinkkiarkistoController::index();
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

$routes->get('/users', function() {
  UserController::all();
});

$routes->get('/user/new', function() {
  UserController::new_user();
});

$routes->post('/user/new', function() {
  UserController::save();
});

$routes->post('/user/:id/password', function($id) {
  UserController::change_password($id);
});

$routes->post('/user/:id/delete', function($id) {
  UserController::delete($id);
});

$routes->get('/user/:id/makeadmin', function($id) {
  UserController::make_admin($id);
});

$routes->get('/user/:id', function($id) {
  UserController::show($id);
});

$routes->get('/drinks', function() {
 ApprovedRecipeController::all();
});

$routes->get('/drink/suggestions', function() {
  SuggestionController::all();
});

$routes->get('/drink/suggestion/new', function() {
  SuggestionController::new_suggestion();
});

$routes->post('/drink/suggestion/new', function() {
  SuggestionController::save();
});

$routes->get('/drink/suggestion/:id/approve', function($id) {
  SuggestionController::approve($id);
});

$routes->get('/drink/suggestion/:id', function($id) {
  SuggestionController::show($id);
});

$routes->get('/drink/new', function() {
  ApprovedRecipeController::new_drink();
});

$routes->post('/drink/new', function() {
  ApprovedRecipeController::save();
});

$routes->get('/drink/:id/edit', function($id) {
  RecipeController::edit($id);
});

$routes->post('/drink/:id/edit', function($id) {
  RecipeController::update($id);
});

$routes->post('/drink/:id/delete', function($id) {
	RecipeController::delete($id);
});

$routes->get('/drink/:id', function($id) {
 RecipeController::show($id);
});


