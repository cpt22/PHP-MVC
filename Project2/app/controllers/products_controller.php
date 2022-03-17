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

        $p = Product::find(1);
        $u = User::find(1);
        $products = $u->products;
        //print_r($products->load());
        $names = $products->includes(['user'])->pluck(['id']);
        print_r($names->load());
        $limited = $names->limit(1);
        print_r($limited->load());
        //print_r($products->load());
        //$names = $products->pluck(["name"]);
        //print_r($products->load());
        $u->save();
        echo "</pre>";
    }

    public function new() {
        $p = Product::create(["name" => "my dog cat", "quantity" => 45]);
        print_r($p);
    }
}