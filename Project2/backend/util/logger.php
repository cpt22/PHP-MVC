<?php
require_once 'http_codes.php';
class Logger {
    function log_error(int $code, string $message = null) {
        global $LOG_MODE, $HTTP_CODES, $TEMPLATE_EXTENSION;

        switch($LOG_MODE) {
            case "debug":
                echo $message ?? $HTTP_CODES[$code];
                die();
                break;
            case "production":
                $subsegment = "errors/" . $code;
                $error_file = "views/" . $subsegment . ".php";
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
