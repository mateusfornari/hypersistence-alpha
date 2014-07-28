<?php

/**
 * @table(book)
 */
class Book extends Hypersistence{
	
	/**
	 * @primaryKey
	 * @column(id)
	 */
	private $id;
	
	/**
	 * @column(person_id)
	 * @manyToOne(lazy) 
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
