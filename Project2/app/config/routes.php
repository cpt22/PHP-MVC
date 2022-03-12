<?php
$BASE_ROUTE = "index";

$routes = array(

    "index" => array(
        "collection" => array (
            "GET" => "index"
        ),
        "member" => array (
            "GET" => "show"
        )
    ),

    "products" => array(
        "collection" => array (
            "GET" => "index"
        ),
        "member" => array (
            "GET" => "show"
        )
    )
);
?>
