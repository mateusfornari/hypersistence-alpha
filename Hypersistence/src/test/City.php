<?php

/**
 * @table(city)
 */
class City extends Hypersistence{
	/**
	 * @primaryKey
	 */
	private $id;
	
	/**
	 * @column()
	 */
	private $name;
	
	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setName($name) {
		$this->name = $name;
	}


}