<?php
$ROOT = dirname(realpath(dirname(__FILE__)));
$BACKEND_BASE = $ROOT . "/backend/";
$APP_BASE = $ROOT . "/app/";
define("BACKEND_BASE", $BACKEND_BASE);
define("APP_BASE", $APP_BASE);

require_once BACKEND_BASE . 'app.php';
App::init();

// Load all relevant php files in backend
$BACKEND_FOLDERS_TO_LOAD = array("db", "helpers", "rendering", "util", "traits", "base_classes", "exceptions");
foreach ($BACKEND_FOLDERS_TO_LOAD as $folder) {
    recursive_load($BACKEND_BASE . $folder);
}

// Load all relevant php files in app
$APP_FOLDERS_TO_LOAD = array("controllers", "models", "helpers");
foreach ($APP_FOLDERS_TO_LOAD as $folder) {
    recursive_load($APP_BASE . $folder);
}


App::$router->route();







/**
 * @param string $base_path
 * @return void
 * Recursively load php files
 */
function recursive_load(string $base_path) {
    if (!file_exists($base_path)) { throw new Exception("Error, could not find '$base_path'"); }
    $scanned_directory = array_diff(scandir($base_path), array('..', '.'));
    $base_path = $base_path . (str_ends_with($base_path, "/") ? "" : "/");
    foreach ($scanned_directory as $file) {
        $path = $base_path . $file;
        if (is_dir($path)) {
            recursive_load($path . "/");
        } else if (preg_match("/^.*.php/", $file)) {
            require_once $path;
        }
    }
}

function fmt_print($item) {
    echo '<pre>';
    print_r($item);
    echo '</pre>';
}


?>