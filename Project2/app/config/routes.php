<?php
$DEFAULT_ROUTE = "index";

resource(name: "products", only: ['index', 'new', 'show', 'edit'], options: [
    'collection' => [
        'stats' => ['get', 'put']
    ],
    'member' => [
        'tmp' => 'post'
    ]
]);
?>
