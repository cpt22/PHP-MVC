<div class="row">
    <div class="col-1">ID</div>
    <div class="col-8">Name</div>
    <div class="col-3">Quantity</div>
</div>
<?php render_partial_collection(partial: "products/index_row", collection: $GLOBALS['products'], as: "product"); ?>
