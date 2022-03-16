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
        echo "<pre>";
        $tmp = Product::new(["name" => "my big dog", "quantity" => 85738957]);
        //print_r($tmp);
        //print_r($tmp::get_db_fields());
        echo $tmp->save(exception: true) ? "true\n":"false";
        print_r($tmp);
        print_r(Product::find(95));
        echo "</pre>";
    }

    public function new() {
        $p = Product::create(["name" => "my dog cat", "quantity" => 45]);
        print_r($p);
    }
}