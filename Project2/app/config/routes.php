<?php
$DEFAULT_ROUTE = "index";

//$routes = [
//
//    "index" => [
//        "collection" => [
//            "GET" => "index"
//        ],
//        "member" => [
//            "GET" => "show"
//        ]
//    ],
//
//    "products" => [
//        "collection" => [
//            "GET" => "index",
//            "POST" => "create"
//        ],
//        "member" => [
//            "GET" => "show"
//        ]
//    ]
//];

resource(name: "products", options: [
    "collection" => [
        "stats" => ["get", "put"]
    ],
    "member" => [
        "tmp" => "post"
    ]
]);
?>
