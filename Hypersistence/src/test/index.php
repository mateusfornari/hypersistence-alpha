<?php

require_once '../hypersistence/Hypersistence.php';
require_once './Person.php';
require_once './Course.php';
require_once './City.php';

//$p = new Student();
//
//$p->setId(3);
//$p->load();

//$b = new Book();
//$b->setId(3);
//$b->load();
//
//var_dump($b->delete());


//$p = new Student();
//$p->setId(47);
//$p->load();

//
//$p->setName('Mateus Bitencourt');
//$p->setEmail('mateusfornari@hotmail.com');
//$p->setNumber('123456');
//
//var_dump($p->save());


//$b = new Book();
//
//$b->setAuthor($p);
//var_dump($b->search()->execute());

//$b->setTitle('Test Book');

//var_dump($p);

//var_dump($p->delete());
//DB::getDBConnection()->commit();

//$c = new Course();
//$c->setId(2);
//$c->load();
//
//var_dump($c);

//$b = new Book();
//$b->setTitle('Bo');
//$books = $b->search()->orderBy('author.city.name')->orderBy('author.id', 'desc')->execute();
//$books->orderBy('author.city.name');
//$books->orderBy('author.id', 'desc');
//$list = $books->execute();

//foreach ($books as $l){
//    $name = $l->getAuthor()->load()->getCity()->load()->getName();
//    echo "{$l->getTitle()} - $name\n";
//}

//var_dump($books);

try{
$p = new Person();
$p->setId(4);
$p->load();
var_dump($p->getBooks());
}  catch (Exception $e){
	var_dump($e->getMessage());
}