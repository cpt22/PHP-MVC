<?php

use JetBrains\PhpStorm\NoReturn;

require_once 'routes.php';
require_once 'config.php';

// Extract the request uri and set the default route if necessary
$stripped_uri = $_SERVER['REQUEST_URI'];
$stripped_uri = str_replace($SITE_BASE_PATH,"", $stripped_uri);
if (empty($stripped_uri)) { $stripped_uri = $DEFAULT_ROUTE; }

$uri_components = explode('/', $stripped_uri);

// Check if the route is defined.
$route = $uri_components[0];
if (!array_key_exists($route, $routes)) { error_page(message: "Error: Route not found."); }

// Check if the request method is defined for that route.
$method = $_SERVER['REQUEST_METHOD'];
if (!array_key_exists($method, $routes[$route])) { error_page(message: "Error: Method not supported for route " . $route); }

$action = $routes[$route][$method];
$controller_file = "controllers/" . $route . "_controller.php";
// Ensure that the appropriate controllers exists.
if (!file_exists($controller_file)) { error_page(code: 500, message: "500 Internal Server Error"); }
require_once $controller_file;

// Check that the action method exists and is callable.
if (!is_callable($action)) { error_page(code: 500, message: "500 Internal Server Error"); }

// Bring in helper methods
require_once 'helpers/helpers.php';
$helpers_route = "helpers/" . $route . "_helpers.php";
if (file_exists($helpers_route)) { include_once $helpers_route; }

// Load Renderers
require_once 'util/renderers.php';

// Call the action method.
$render_called = false;
($action)();

if (!$render_called) {
    $top_view_partial
    render_partial($)
}
//print_r($_SERVER);
//print_r($_GET);

// Generate a 404 message with supplied message
#[NoReturn] function error_page(int $code = 404, string $message = "404 Page not Found") {
    echo $message;
    http_response_code($code);
    die();
}
?>
