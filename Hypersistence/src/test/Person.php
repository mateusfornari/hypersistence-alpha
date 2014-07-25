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

	public function setId($id) {
		$this->id = $id;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function setBooks($books) {
		$this->books = $books;
	}


}

/**
 * @table(student)
 * @joinColumn(id)
 */
class Student extends Person{
	/**
	 * @column()
	 */
	private $number;
	public function getNumber() {
		return $this->number;
	}

	public function setNumber($number) {
		$this->number = $number;
	}


}

/**
 * @table(book)
 */
class Book extends Hypersistence{
	
	/**
	 * @column(id)
	 * @primaryKey 
	 */
	private $id;
	
	/**
	 * @column(person_id)
	 * @manyToOne(eager) 
	 * @itemClass(Person)
	 */
	private $author;
	
	/**
	 * @column()
	 */
	private $title;
	public function getId() {
		return $this->id;
	}

	public function getAuthor() {
		return $this->author;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setAuthor($author) {
		$this->author = $author;
	}

	public function setTitle($title) {
		$this->title = $title;
	}


}