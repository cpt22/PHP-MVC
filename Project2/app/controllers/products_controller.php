<?php

class ProductsController
{
    public function __construct() {

    }

    public function index() {
        echo "<pre>";
        $prod = Product::find(114);
        print_r($prod);
        $prod->quantity = 26345989;
        print_r($prod);
        print_r($prod->save());
        print_r(Product::all()->pluck(["id", "quantity"])->value);
        echo "</pre>";
        $GLOBALS['products'] = [];
        //$GLOBALS['products'] = Product::all();
    }

    public function show() {
        $GLOBALS['product'] = Product::find($_REQUEST['id']);
    }
}