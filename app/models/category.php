<?php

class Category extends BaseModel {
	public $id, $name;

	public static function get_id($name) {

		$query = DB::connection()->prepare('SELECT id FROM Category WHERE name = :name LIMIT 1;');
		$query->execute(array('name' => $name));
		$row = $query->fetch();

		if($row) {
			return $row['id'];
		}

		return null;
	}

	public static function all() {
		$query = DB::connection()->prepare('SELECT id, name FROM Category;');
		$query->execute();
		$rows = $query->fetchAll();

		$categories = array();
		foreach ($rows as $row) {
			$categories[] = new Category(array('id' => $row['id'], 'name' => $row['name']));
		}

		return $categories;
	}

}