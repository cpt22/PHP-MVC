<?php
class Product extends Model
{
    protected function setup() {
        $this->validates("name", ["presence" => true]);
        $this->belongs_to("user", inverse_of: "products");
        $this->after_create('say_dog');
    }

    protected function say_dog() {
        print_r("I AM DOG");
    }
}
?>