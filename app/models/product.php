<?php
class Product extends ApplicationModel
{
    protected function setup()
    {
        $this->belongs_to("user", inverse_of: "product");
        $this->validates("name", ["presence" => true]);
        //$this->has_many("users", inverse_of: "product");
    }
}
?>