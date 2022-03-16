<?php
$ROOT = dirname(realpath(dirname(__FILE__)));
$BACKEND_BASE = $ROOT . "/backend/";
$APP_BASE = $ROOT . "/app/";
define("BACKEND_BASE", $BACKEND_BASE);
define("APP_BASE", $APP_BASE);

require_once $BACKEND_BASE . 'app.php';
App::init();

?>