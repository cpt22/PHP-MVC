<?php
$SELECTED_LAYOUT = "template";

class IndexController extends Controller {
    public function __construct() {

    }

    public function index() {}

    public function show() {
        print_r(Product::find($_REQUEST['id']));
    }
}

?>
