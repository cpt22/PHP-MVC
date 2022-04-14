<?php

class ProductsController extends ApplicationController
{
    public function __construct() {

    }

    public function index() {
        $GLOBALS['products'] = Product::all();
    }

    public function show() {
        $GLOBALS['product'] = Product::find($_REQUEST['id'], exception: true);
        echo "<pre>";
        echo "</pre>";
    }

    public function new() {
        $p = Product::create(["name" => "my dog cat", "quantity" => 45]);
        print_r($p);
    }

    public function edit() {
        $GLOBALS['product'] = Product::find($_REQUEST['id'], exception: true);
    }
}