<?php
require_once 'http_codes.php';
class Logger {
    function log_error(int $code, string $message = null) {
        global $HTTP_CODES;

        switch(App::$config->log_mode) {
            case "debug":
                echo $message ?? $HTTP_CODES[$code];
                die();
                break;
            case "production":
                $subsegment = "errors/" . $code;
                $error_file = "views/" . $subsegment . App::$config->template_extension;
                if (file_exists($error_file)) {
                    render(file: $subsegment);
                } else {
                    echo $HTTP_CODES[$code];
                }
                die();
                break;
        }
    }
}
?>
