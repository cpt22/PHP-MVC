<?php

class User extends ApplicationModel
{
    protected function setup() {
        //$this->has_many("products", );
        $this->has_many("products", inverse_of: "user");
    }
}