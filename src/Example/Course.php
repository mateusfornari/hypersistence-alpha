<?php
/**
 * @table(course)
 */
class Course extends BaseModel{
    /**
     * @primaryKey 
     */
	private $id;
    /**
     * @column() 
     */
	private $description;
    
    /**
     * @manyToMany(eager)
     * @joinTable(student_has_course)
     * @joinColumn(course_id)
     * @inverseJoinColumn(student_id)
     * @itemClass(Student) 
     */
    private $students;
	
	
	public function getId() {
		return $this->id;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

    public function getStudents() {
        return $this->students;
    }
	
	public function setStudents($students) {
		$this->students = $students;
	}

}
?>