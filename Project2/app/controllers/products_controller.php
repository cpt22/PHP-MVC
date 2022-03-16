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
        //$tmp = Product::new(["name" => "my big dog", "quantity" => 85738957]);
        //print_r($tmp);
        //print_r($tmp::get_db_fields());
        //$tmp->save(exception: true);

        $tmp = Product::find(12);
        $tmp->save(exception: true);

        $t = Product::create(["name" => "Will", "quantity" => 400]);
        print_r($t);
        $t->name = "Will 2";
        print_r($t);
        $t->save();

        print_r(Product::all()->where(["id=:id"], ["id" => $t->id]));
        //print_r($tmp->users);
        //print_r($tmp);
        //print_r(Product::find(95));
        echo "</pre>";
    }

    public function new() {
        $p = Product::create(["name" => "my dog cat", "quantity" => 45]);
        print_r($p);
    }
}