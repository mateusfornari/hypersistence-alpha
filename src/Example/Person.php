<?php

/**
 * @table(person)
 */
class Person extends Hypersistence{
	
	/**
	 * @primaryKey 
	 */
	protected $id;
	/**
	 * @column() 
	 */
	protected $name;
	/**
	 * @column() 
	 */
	protected $email;
	
	/**
	 * @manyToOne(lazy)
	 * @itemClass(City)
	 * @column(city_id) 
	 */
	protected $city;


	/**
	 * @oneToMany(lazy)
	 * @itemClass(Book)
	 * @joinColumn(person_id) 
	 */
	protected $books;
	
	
	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getBooks() {
		return $this->books;
	}
	
	public function setBooks($books) {
		$this->books = $books;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function getCity() {
		return $this->city;
	}

	public function setCity($city) {
		$this->city = $city;
	}



}



