<?php
class Product extends Model
{
    protected function setup() {
        $this->validates("name", ["presence" => true]);
    }
}
?>