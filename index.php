<?php

require_once __DIR__ . "/vendor/autoload.php";

use Source\datalayer\DataLayer;
use Source\Test\Product;

$dataLayerProduct = new DataLayer(
    "products",
    Product::class,
    [
        "meuId" => "id",
        "value" => "value",
        "description" => "description",
        "name" => "name"
    ],
    ["created_at", "update_at"]
);
$product = $dataLayerProduct->all();
$product = $dataLayerProduct->loadAll($product);

var_dump($product[1]->getName());