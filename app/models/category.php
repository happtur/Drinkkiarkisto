<?php

class Category extends BaseModel {

	public static function getId($name) {
		
		$query = DB::connection()->prepare('SELECT id FROM Category WHERE name = :name LIMIT 1;');
		$query->execute(array('name' => $name));
		$row = $query->fetch();

		if($row) {
			return $row['id'];
		}

		return null;
	}

	public static function all() {

	}

}