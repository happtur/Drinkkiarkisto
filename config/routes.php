<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });

  $routes->get('/drinks', function() {
  	HelloWorldController::list_drinks();
  });

  $routes->get('/drink/1', function() {
  	HelloWorldController::show_drink();
  });

  $routes->get('/drink/1/edit', function() {
  	HelloWorldController::edit_drink();
  });

