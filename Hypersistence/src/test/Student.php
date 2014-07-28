<?php

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
     * @joinTable(student_has_course)
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
