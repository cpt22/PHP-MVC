<?php

class ProductsController
{
    public function __construct() {

    }

    public function index() {
        $GLOBALS['products'] = Product::all();
    }

    public function show() {
        $GLOBALS['product'] = Product::find($_REQUEST['id']);//Product::where(array("id=:id"), array("id" => $_REQUEST['id']));
    }
}