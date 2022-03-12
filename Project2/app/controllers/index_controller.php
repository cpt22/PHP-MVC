<?php
$SELECTED_LAYOUT = "template";

class IndexController {
    public function __construct() {

    }

    public function index() {}

    public function show() {
        phpinfo();
        $dog = Product::find(2);
        print_r($dog);
    }
}

?>
