<?php
require_once APP_BASE . 'config/config.php';
require_once BACKEND_BASE . 'routing/router.php';
require_once BACKEND_BASE . 'db/db.php';

class App {
    public static ?App $app = null;
    public static Router $router;
    public static Config $config;
    public static DB $db;

    private function __construct() {

    }

    public static function init() {
        if (self::$app == null) {
            self::$app = new App();
            self::$config = new Config();
            self::$router = new Router();
            self::$db = new DB();

            self::$router->init();
        }
    }
}

function fmt_print($item) {
    echo '<pre>';
    print_r($item);
    echo '</pre>';
}