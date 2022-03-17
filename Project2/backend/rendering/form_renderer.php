<?php

function form_for(object $object, string $name = null, callable $do = null) {
    if ($object == null || $do == null) {
        throw new Exception("REPLACE THIS: Form object and callback must exist");
    };
    $form = new FormBuilder();
    $do($form);
}