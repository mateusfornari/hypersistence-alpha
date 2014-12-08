<?php

require_once '../Hypersistence/Hypersistence.php';

Hypersistence::registerAutoloader();


require_once './Person.php';
require_once './Book.php';
require_once './Student.php';
require_once './Course.php';
require_once './City.php';

//SAVE
$c = new City();
$c->setName('San Francisco');

$c->save();



$s = new Student();
$s->setCity($c);
$s->setEmail('test@hypersistence.com');
$s->setName('Mateus Fornari');
$s->setNumber('123456');

var_dump($s->save());

exit();
$course = new Course();
$course->setDescription('PHP Programming');

$course->save();

$s->addCourses($course);



$p = new Person();
$p->setCity($c);
$p->setEmail('other@hypersistence.com');
$p->setName('Other Person');

$p->save();



$b = new Book();
$b->setAuthor($p);
$b->setTitle('PHP Book');

$b->save();
Hypersistence::commit();


//LOAD
$p = new Person();
$p->setId(2);
$p->load();

echo $p->getName()."\n";

$books = $p->getBooks()->execute();

foreach ($books as $b){
	echo $b->getTitle()."\n";
}

$s = new Student();
$s->setId(1);
$s->load();

echo $s->getName()."\n";

$courses = $s->getCourses();
foreach ($courses as $c){
	echo $c->getDescription()."\n";
	$course = $c;
}

$s->deleteCourses($course);
Hypersistence::commit();
$s->load(true);

$courses = $s->getCourses();
foreach ($courses as $c){
	echo $c->getDescription()."\n";
}

$c = new Course();
$c->setId(3);
$c->load();

echo $c->getDescription()."\n";

$students = $c->getStudents();
foreach ($students as $s){
	echo $s->getName()."\n";
}


//SEARCH

$p = new Person();

$p->setName('Mat');

$search = $p->search();
$search->orderBy('name');

$list = $search->execute();
foreach ($list as $p){
	echo $p->getName()."\n";
}

//Recursive Search
$c = new City();
$c->setName('San');

$p = new Person();
$p->setCity($c);

$b = new Book();
$b->setAuthor($p);

$list = $b->search()->execute();
foreach ($list as $b){
	echo $b->getTitle()."\n";
}
