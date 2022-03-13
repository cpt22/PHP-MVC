<?php
trait HasAssociations {
    private array $associations = [];

    function has_many(string $association_name, string $inverse_of, string $through = "", string $class_name = "") {
        if (array_key_exists($association_name, $this->associations)) { new Exception("Association $association_name already exists."); }
        $this->associations[$association_name] = new Association(type: "many", inverse: $inverse_of, through: $through, class: $class_name);
    }

    function has_one(string $association_name, string $inverse_of, string $class_name = "") {

    }
}