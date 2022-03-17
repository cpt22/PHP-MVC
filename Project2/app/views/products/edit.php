<?php
$product = $GLOBALS['product'];
form_for(object: $product, do: function($form) {
    $form->label(for: "name");
    $form->text_area("name");
});
?>