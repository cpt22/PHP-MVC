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

    protected function is_association(string $var_name): bool
    {
        return array_key_exists($var_name, $this->associations);
    }

    protected function has_many(string $association_name, string $inverse_of, string $through = "", string $class = "")
    {
        if (array_key_exists($association_name, $this->associations)) { new Exception("Association $association_name already exists."); }
        $this->associations[$association_name] = new HasManyAssociation(owner: $this, name: $association_name,
            inverse: $inverse_of, through: $through, class: $class);
    }

    protected function has_one(string $association_name, string $inverse_of, string $through = "", string $class = "")
    {
        if (array_key_exists($association_name, $this->associations)) { new Exception("Association $association_name already exists."); }
        $this->associations[$association_name] = new HasOneAssociation(owner: $this, name: $association_name,
            inverse: $inverse_of, through: $through, class: $class);
    }

    protected function belongs_to(string $association_name, string $inverse_of, string $through = "", string $class = "")
    {
        if (array_key_exists($association_name, $this->associations)) { new Exception("Association $association_name already exists."); }
        $this->associations[$association_name] = new BelongsToAssociation(owner: $this, name: $association_name,
            inverse: $inverse_of, through: $through, class: $class);
    }

    protected function get_association_objects(string $name) {
        return $this->associations[$name]->get();
    }

    protected function set_on_association(string $name, mixed $value) {

    }
}