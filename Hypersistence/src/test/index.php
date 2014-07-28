<?php

require_once '../hypersistence/Hypersistence.php';
require_once './Person.php';
require_once './Course.php';

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

$b = new Book();
$b->setTitle('Bo');
$books = $b->search();
$books->orderBy('author.name');
$books->orderBy('author.id', 'desc');
$list = $books->execute();

foreach ($list as $l){
    $name = $l->getAuthor()->load()->getName();
    echo "{$l->getTitle()} - $name - {$l->getAuthor()->getId()}\n";
}

//var_dump($books);