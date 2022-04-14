<?php

class HasOneAssociation extends Association
{

    public function __construct(BaseModel $owner, string $name, string $inverse, string $through = null,
                                string $class = null, string $foreign_key = null)
    {
        parent::__construct($owner, $name, $inverse, $through, $class, $foreign_key);
        $this->type = Association::ONE_TYPE;

        if (empty($foreign_key)) {
            $this->foreign_key = Inflector::underscore($this->owner::model_name()) . "_id";
        } else {
            $this->foreign_key = $foreign_key;
        }
    }

    public function get(): mixed
    {
        $class_name = $this->class_name::model_name();

        $associated_object = App::$store->lookup(model_name: $class_name, attribute: $this->foreign_key,
            value: $this->owner->id, singular: true);
        if ($associated_object == null) {
            $this->data = $class_name::find_by(value: $this->owner->id, attribute: $this->foreign_key);
        }
        $this->data = $associated_object;
        return $this->data;
    }

    public function set(mixed $value) {
        $this->data = $value;
    }
}