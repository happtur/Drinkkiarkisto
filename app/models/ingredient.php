<?php

class Ingredient extends BaseModel {
	public $name, $amount;

	public function __construct($attributes) {
		parent::__construct($attributes);
	}


}