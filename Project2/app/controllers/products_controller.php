<?php

class ProductsController
{
    public function __construct() {

    }

    public function index() {
        $GLOBALS['products'] = Product::all();
    }

    public function show() {
        $GLOBALS['product'] = Product::find($_REQUEST['id']);
    }

    public function new() {
        $p = Product::create(["name" => "my dog cat", "quantity" => 45]);
        print_r($p);
    }
}