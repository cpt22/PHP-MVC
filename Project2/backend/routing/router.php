<?php
use JetBrains\PhpStorm\NoReturn;

require_once $APP_BASE . "config/routes.php";

// Extract the request uri and set the default route if necessary
$stripped_uri = $_SERVER['REDIRECT_URL'];
$stripped_uri = str_replace($SITE_BASE_PATH,"", $stripped_uri);
if (empty($stripped_uri)) { $stripped_uri = $DEFAULT_ROUTE; }

$uri_components = explode('/', $stripped_uri);

// Check if the route is defined.
$route = $uri_components[0];
if (!array_key_exists($route, $routes)) { $logger->log_error(code: 404, message: "Route not found."); }

$is_member = false;
$member_id = null;
if (count($uri_components) > 1 && preg_match("/^[0-9]*/", $uri_components[1]))
{
    $is_member = true;
    $member_id = $uri_components[1];
    $_GET['id'] = $member_id;
}

$route_block = $routes[$route][$is_member ? "member" : "collection"];
if (empty($route_block)) { $logger->log_error(code: 404, message: "No route defined in the " . ($is_member ? "member" : "collection") . " route block!"); }

// Check if the request method is defined for that route.
$method = $_POST['REQUEST_METHOD'] ?? $_SERVER['REQUEST_METHOD'];
if (!array_key_exists($method, $route_block)) { $logger->log_error(code: 404, message: "Method not supported for route $route"); }

$action = $route_block[$method];
$controller_file = $APP_BASE . "controllers/" . $route . "_controller.php";
// Ensure that the appropriate controllers exists.
if (!file_exists($controller_file)) { $logger->log_error(code: 404, message: "No <b>$route</b> controller defined."); }
require_once $controller_file;

// Check that the appropriate action function exists and is callable.
if (!is_callable($action)) { $logger->log_error(code: 404, message: "No method defined for <b>$action</b> in <b>$route</b> controller."); }

// Bring in helper methods
include_once 'helpers/helpers.php';
$helpers_route = $APP_BASE . "helpers/" . $route . "_helpers.php";
if (file_exists($helpers_route)) { include_once $helpers_route; }

// TODO: Might not need
$params = $_REQUEST;

// Call the action method.
$render_called = false;
($action)();

if (!$render_called)
{
    $top_view_partial =  $route . "/" . $action;
    render($top_view_partial);
}
?>
