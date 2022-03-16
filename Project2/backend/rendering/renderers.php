<?php
/**
 * @param string $file - The layout file to be rendered
 * @param array $locals - The values passed to fill in areas of the template.
 * @return string - The string representation of the HTML document.
 * Render the layout
 */
function render_layout(string $file, array $locals = []) {
    global $params;
    $layout = '';

    foreach($locals AS $key => $value) {
        ${$key} = $value;
    }

    ob_start();
    require $GLOBALS['APP_BASE'] . "views/layouts/" . $file . App::$config->template_extension;
    $layout .= ob_get_contents();
    ob_end_clean();

    return $layout;
}

/**
 * @param string $file - The view file to be rendered
 * @param array $locals - The locals to be passed to the view file
 * @return void
 * Renders a top level template.
 */
function render(string $file, array $locals = []) {
    global $render_called, $SELECTED_LAYOUT, $params;
    $render_called = true;

    $contents = '';

    foreach($locals AS $key => $value) {
        ${$key} = $value;
    }

    ob_start();
    require $GLOBALS['APP_BASE'] . "views/" . $file . App::$config->template_extension;
    $contents .= ob_get_contents();
    ob_end_clean();

    if (!empty($SELECTED_LAYOUT ?? App::$config->selected_layout)) {
        echo render_layout($SELECTED_LAYOUT ?? App::$config->selected_layout, locals: array_merge($locals, array("content" => $contents)));
    } else {
        echo $contents;
    }
}

/**
 * @param string $partial - The partial to be rendered
 * @param array $locals - Local variables passed to the partial
 * @param bool $output - Whether the contents of the partial should be echoed or returned as a string
 * @return string|void
 * Renders the specified partial
 */
function render_partial(string $partial, array $locals = [], bool $output = true) {
    global $params;
    $contents = '';
    $partial = fix_partial_path($partial);

    foreach ($locals AS $key => $value) {
        ${$key} = $value;
    }

    ob_start();
    require $GLOBALS['APP_BASE'] . "views/" . $partial;
    $contents .=ob_get_contents();
    ob_end_clean();

    if ($output) {
        echo $contents;
    } else {
        return $contents;
    }
}

/**
 * @param string $partial - The partial to use in rendering the collection
 * @param array $collection - The collection to be rendered
 * @param string $as - The name of the object variable
 * @param array $locals - Local variables passed to the partial
 * @param bool $output - Whether the contents should be echoed or returned as a string
 * @return string|void
 * Renders a collection of objects using the provided partial
 */
function render_partial_collection(string $partial, $collection, string $as, array $locals = [], bool $output = true) {
    global $params;
    $contents = '';
    $partial = fix_partial_path($partial);

    foreach($locals AS $key => $value) {
        ${$key} = $value;
    }

    ${$as . '_counter'} = 0;
    foreach($collection AS $object) {
        ${$as} = $object;

        ob_start();
        include $GLOBALS['APP_BASE'] . "views/" . $partial;
        $contents .= ob_get_contents();
        ob_end_clean();

        ${$as . '_counter'}++;
    }

    if ($output) {
        echo $contents;
    } else {
        return $contents;
    }
}

/**
 * @param string $path
 * @return string
 * Adjust the pathing of partials to append the extension and the underscore to the partial file name
 */
function fix_partial_path(string $path) {
    $vals = explode("/", $path);
    $tmp = array_pop($vals);
    $tmp = "_" . $tmp . App::$config->template_extension;
    $vals[] = $tmp;
    return join("/", $vals);
}
?>