<?php
$product = $GLOBALS['product'];
form_for(object: $product, method: "post", do: function($form) {
    $form->label(for: "name");
    $form->text_field("name", class: "form-control", html_options: [], maxlength: "10");
});
?>