<?php
function render_partial($file, $data = false, $locals = array()) {
    global $render_called;
    $render_called = true;
    $contents = '';

    foreach($locals AS $key => $value) {
        ${$key} = $value;
    }

    //${$name . '_counter'} = 0;
    foreach($data AS $object) {
        ${$name} = $object;

        ob_start();
        include "views/" . $file;
        $contents .= ob_get_contents();
        ob_end_clean();

        //${$name . '_counter'}++;
    }

    return $contents;
}
?>