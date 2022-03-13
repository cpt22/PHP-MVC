<?php
$ROOT = dirname(realpath(dirname(__FILE__)));
$BACKEND_BASE = $ROOT . "/backend/";
$APP_BASE = $ROOT . "/app/";

$GLOBALS['ROOT'] = $ROOT;
$GLOBALS['BACKEND_BASE'] = $BACKEND_BASE;
$GLOBALS['APP_BASE'] = $APP_BASE;

// Load config
require_once $APP_BASE . "config/config.php";

// Load all relevant php files in backend
$BACKEND_FOLDERS_TO_LOAD = array("base_classes", "db", "helpers", "models", "rendering", "util");
foreach ($BACKEND_FOLDERS_TO_LOAD as $folder) {
    recursive_load($BACKEND_BASE . $folder);
}

// Load all relevant php files in app
$APP_FOLDERS_TO_LOAD = array("controllers", "models");
foreach ($APP_FOLDERS_TO_LOAD as $folder) {
    recursive_load($APP_BASE . $folder);
}

$logger = new Logger();

// Create the DB
$db = new DB();

// Create the object store
$store = new ObjectStore();
$GLOBALS['store'] = $store;

// Handoff to router
require $BACKEND_BASE . "routing/router.php";

/**
 * @param string $base_path
 * @return void
 * Recursively load php files
 */
function recursive_load(string $base_path) {
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

?>

