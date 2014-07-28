<?php

/**
 * @table(person)
 */
class Person extends Hypersistence{
	
	
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
	 * @oneToMany(eager)
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
	public function getCity() {
		return $this->city;
	}

	public function setCity($city) {
		$this->city = $city;
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
    
    /**
     * @manyToMany(eager)
     * @joinTable(student_course)
     * @joinColumn(student_id)
     * @inverseJoinColumn(course_id)
     * @itemClass(Course)
     */
    private $courses;
    
	public function getNumber() {
		return $this->number;
	}

	public function setNumber($number) {
		$this->number = $number;
	}

    public function getCourses() {
        return $this->courses;
    }

    public function setCourses($courses) {
        $this->courses = $courses;
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
	 * @manyToOne(lazy) 
	 * @itemClass(Person)
	 */
	private $author;
	
    /**
	 * @column(student_id)
	 * @manyToOne(lazy) 
	 * @itemClass(Student)
     * @nullable
	 */
	private $student;
    
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

    public function getStudent() {
        return $this->student;
    }

    public function setStudent($student) {
        $this->student = $student;
    }


}