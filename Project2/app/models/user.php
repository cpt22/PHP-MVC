<?php

class User extends BaseModel
{
    protected function setup() {
        //$this->has_many("products", );
        $this->has_many("products", inverse_of: "user");
    }
}