<?php
require_once 'dbconfig.php';
require 'models/model.php';
$db = mysqli_connect($DB_HOSTNAME, $DB_USER, $DB_PASSWORD, $DB_DB);
if($db->connect_error) {
    exit('Error connecting to database');
}

$test = new model();
$test->cutis = "my dog is big";
$test->create();

$db->set_charset("utf8mb4");
?>