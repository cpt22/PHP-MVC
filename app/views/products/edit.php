<?php
$product = $GLOBALS['product'];
form_for(object: $product, url: App::$router->update_product_path($product), method: "patch", do: function($form) use ($product) {
    $form->label(for: "name");
    $form->password_field("quantity", class: "form-control", maxlength: "10");
    $form->submit();
});
?>