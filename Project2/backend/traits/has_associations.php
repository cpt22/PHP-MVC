<?php
trait HasAssociations {
    /*
     * Association structure:
     * [
     *      <association name> => [
     *          "type" => <association type>,
     *          "inverse" => <name of inverse>,
     *          "through" => <through assoc>,
     *          "class" => <model class if different than association name>
     *      ]
     *  ]
     */
    private array $associations = [];

    protected function is_association(string $var_name)
    {
        return array_key_exists($var_name, $this->associations);
    }

    protected function has_many(string $association_name, string $inverse_of, string $through = "", string $class_name = "")
    {
        if (array_key_exists($association_name, $this->associations)) { new Exception("Association $association_name already exists."); }
        $this->associations[$association_name] = new Association(type: "many", inverse: $inverse_of, through: $through, class: $class_name);
    }

    protected function has_one(string $association_name, string $inverse_of, string $class_name = "")
    {

    }

    protected function belongs_to(string $model_name, string $inverse_of)
    {

    }
}