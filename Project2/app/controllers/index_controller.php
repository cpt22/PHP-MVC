<?php
$SELECTED_LAYOUT = "template";

class IndexController extends Controller {
    public function __construct() {

    }

    public function index() {}

    public function show() {
        $dog = Product::where(array("id>:num"), array("num" => $_REQUEST['num']));
        echo($dog);
        print_r($dog[0]);
        echo($dog);
    }
}

?>
