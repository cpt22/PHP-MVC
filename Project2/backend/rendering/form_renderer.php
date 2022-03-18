<?php

function form_for(object $object, string $url = null, string $method = "post", string $name = null, callable $do = null)
{
    if ($object == null || $do == null) {
        throw new Exception("REPLACE THIS: Form object and callback must exist");
    }
    $form = new FormBuilder($object);
    $action = $url ?? "calculate url here";

    $method = strtolower($method);
    if ($method == "post" || $method == "get") {
        $real_method = $method;
    } else {
        $real_method = "post";
        $custom_method_html = "<input type=\"hidden\" name=\"REQUEST_METHOD\" value=\"$method\" />";
    }

    echo "<form action=\"$action\" accept-charset=\"UTF-8\" method=\"$real_method\">";
    if (isset($custom_method_html)) {
        echo $custom_method_html;
    }

    $do($form);

    echo "</form>";
}

?>