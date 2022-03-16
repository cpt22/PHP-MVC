<?php
$DEFAULT_ROUTE = "index";

resource(name: "products", options: [
    "collection" => [
        "stats" => ["get", "put"]
    ],
    "member" => [
        "tmp" => "post"
    ]
]);
?>
