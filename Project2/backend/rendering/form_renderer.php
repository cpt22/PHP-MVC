<?php

function form_for(object $object, string $path = null, string $method = "post", string $name = null, callable $do = null) {
    if ($object == null || $do == null) {
        throw new Exception("REPLACE THIS: Form object and callback must exist");
    }
    $form = new FormBuilder($object);
    $do($form);
}

?>