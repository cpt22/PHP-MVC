<?php
class Product extends BaseModel
{
    protected function setup()
    {
        $this->validates("name", ["presence" => true]);
        $this->has_many("users", inverse_of: "product");
    }
}
?>