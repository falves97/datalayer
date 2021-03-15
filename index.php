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
    ["created_at", "update_at"],
    "meuId"
);



$products = $dataLayerProduct->all(30, 2);
foreach ($products as $p) {
    $productAux = $dataLayerProduct->loadObject($p);
    $dataLayerProduct->destroy(["meuId" => $productAux->getMeuId()]);
    var_dump($p);
}



