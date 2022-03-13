<?php
$BASE_ROUTE = "index";

$routes = [

    "index" => [
        "collection" => [
            "GET" => "index"
        ],
        "member" => [
            "GET" => "show"
        ]
    ],

    "products" => [
        "collection" => [
            "GET" => "index",
            "POST" => "create"
        ],
        "member" => [
            "GET" => "show"
        ]
    ]
];
?>
