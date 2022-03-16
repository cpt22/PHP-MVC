<?php
use JetBrains\PhpStorm\NoReturn;

$BLANK_PATH_ROUTE = "BLANK_PATH_ROUTE";
$DEFAULT_RESOURCE_ROUTES = [
    "index" => [
        "path" => $BLANK_PATH_ROUTE,
        "method" => "get",
        "action" => "index",
        "type" => "collection"
    ],
    "new" => [
        "path" => "new",
        "method" => "get",
        "action" => "new",
        "type" => "collection"
    ],
    "create" => [
        "path" => $BLANK_PATH_ROUTE,
        "method" => "post",
        "action" => "create",
        "type" => "collection"
    ],
    "show" => [
        "path" => $BLANK_PATH_ROUTE,
        "method" => "get",
        "action" => "show",
        "type" => "member"
    ],
    "edit" => [
        "path" => "edit",
        "method" => "get",
        "action" => "edit",
        "type" => "member"
    ],
    "update" => [
        "path" => $BLANK_PATH_ROUTE,
        "method" => "patch",
        "action" => "update",
        "type" => "member"
    ],
    "delete" => [
        "path" => $BLANK_PATH_ROUTE,
        "method" => "delete",
        "action" => "delete",
        "type" => "member"
    ]
];

class Router {
    private array $default_resource_routes;
    private string $blank_path_route;
    private array $routes = [];

    public function __construct() {
        global $DEFAULT_RESOURCE_ROUTES, $BLANK_PATH_ROUTE;
        $this->blank_path_route = $BLANK_PATH_ROUTE;
        $this->default_resource_routes = $DEFAULT_RESOURCE_ROUTES;
    }

    /**
     * @return void
     */
    public function init() {
        require_once APP_BASE . "/config/routes.php";
    }

    /**
     * @param string $name
     * @param array $except
     * @param array $only
     * @param array $options
     * @return void
     */
    public function resource(string $name, array $except = [], array $only = [], array $options = []) {
        $default_routes = $this->default_resource_routes;
        $temp = [
            "collection" => [],
            "member" => []
        ];
        if (!empty($only)) {
            $default_routes = array_intersect_key($default_routes, array_flip($only));
        } else if (!empty($except)) {
            $default_routes = array_diff_key($default_routes, array_flip($except));
        }

        foreach ($default_routes as $action => $body) {
            $type = $body['type'];
            $path = $body['path'];
            $method = strtolower($body['method']);
            $temp[$type][$path][$method] = $action;
        }

        foreach(['collection', 'member'] as $type) {
            $o = $options[$type];
            if (!isset($o)) { continue; }
            foreach($o as $route_name => $val) {
                if (is_array($val)) {
                    $val = array_unique($val);
                    foreach($val as $method) {
                        $method = strtolower($method);
                        $temp[$type][$route_name][$method] = $route_name;
                    }
                } else {
                    $val = strtolower($val);
                    $temp[$type][$route_name][$val] = $route_name;
                }
            }
        }

        $this->routes[$name] = $temp;
    }
}

function resource(string $name, array $except = [], array $only = [], array $options = []) {
    App::$router->resource($name, $except, $only, $options);
}


function url_for(string $model_name, string $path, string $method="get", array $params = []) {

    $param_string = "";
    $method = strtolower($method);
    if (!empty($params)) {
        $param_string = "?" . http_build_query($params);
    }

    //require_once APP_BASE . "config/routes.php";

}


/*
fmt_print(url_for(model_name: "test", path: "test", params: ["nam  e" => "dog cat query kjehdakjhdakjEFR®´´∂"]));

// Extract the request uri and set the default route if necessary
$stripped_uri = $_SERVER['REDIRECT_URL'];
$stripped_uri = str_replace($SITE_BASE_PATH,"", $stripped_uri);
if (empty($stripped_uri)) { $stripped_uri = $DEFAULT_ROUTE; }

$uri_components = explode('/', $stripped_uri);

// Check if the route is defined.
$route = $uri_components[0];
if (!array_key_exists($route, $ROUTES)) { $logger->log_error(code: 404, message: "Route not found."); }

$is_member = false;
$member_id = null;
if (count($uri_components) > 1 && preg_match("/^[0-9]*$/", $uri_components[1]))
{
    $is_member = true;
    $member_id = $uri_components[1];
    $_REQUEST['id'] = $member_id;
}

$route_block = $ROUTES[$route][$is_member ? "member" : "collection"];
if (empty($route_block)) { $logger->log_error(code: 404, message: "No route defined in the " . ($is_member ? "member" : "collection") . " route block!"); }


// Check that the sub-route exists
$sub_route = $BLANK_PATH_ROUTE;
if (!$is_member && isset($uri_components[1])) {
    $sub_route = $uri_components[1];
} else if ($is_member && isset($uri_components[2])) {
    $sub_route = $uri_components[2];
}
if (!array_key_exists($sub_route, $route_block)) { $logger->log_error(code: 404, message: "Sub-route not found $sub_route"); }
$sr_block = $route_block[$sub_route];

// Check if the request method is defined for that route.
$method = strtolower($_POST['REQUEST_METHOD'] ?? $_SERVER['REQUEST_METHOD']);
if (!array_key_exists($method, $sr_block)) { $logger->log_error(code: 404, message: "Method not supported for route $route/$sub_route"); }

$action = $sr_block[$method];
$controller_file = $APP_BASE . "controllers/" . $route . "_controller.php";
// Ensure that the appropriate controllers exists.
if (!file_exists($controller_file)) { $logger->log_error(code: 404, message: "No <b>$route</b> controller defined."); }
require_once $controller_file;

$ctlr_class_name = ucfirst($route) . "Controller";
$controller = new $ctlr_class_name;

// Check that the appropriate action function exists and is callable.
if (!is_callable(array($controller, $action))) { $logger->log_error(code: 404, message: "No method defined for <b>$action</b> in <b>$route</b> controller."); }

// Bring in helper methods
include_once 'helpers/helpers.php';
$helpers_route = $APP_BASE . "helpers/" . $route . "_helpers.php";
if (file_exists($helpers_route)) { include_once $helpers_route; }

// TODO: Might not need
$params = $_REQUEST;

// Call the action method.
$render_called = false;
$controller->{$action}();

if (!$render_called)
{
    $top_view_partial =  $route . "/" . $action;
    render($top_view_partial);
}
*/
?>
