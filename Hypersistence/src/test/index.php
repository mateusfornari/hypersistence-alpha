<?php

require_once '../hypersistence/Hypersistence.php';
require_once './Person.php';

//$p = new Student();
//
//$p->setId(3);
//$p->load();

//$b = new Book();
//$b->setId(3);
//$b->load();
//
//var_dump($b->delete());


$p = new Person();
$p->setId(18);
//$p->load();

//
//$p->setName('Mateus Bitencourt');
//$p->setEmail('mateusfornari@hotmail.com');
//$p->setNumber('123456');
//
//var_dump($p->save());


$b = new Book();

$b->setAuthor($p);
var_dump($b->search()->execute());

//$b->setTitle('Test Book');

//var_dump($b);

//var_dump($p->delete());
//DB::getDBConnection()->commit();

