<?php

require_once __DIR__ . "/vendor/autoload.php";

use Source\Test\Product;

$p = new Product();

//$p = 1;
//$p->load([id => 1]);

//echo $p->getName();
//
var_dump($p->load(["id" => 1]));