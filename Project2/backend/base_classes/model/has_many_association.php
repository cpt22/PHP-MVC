<?php

class HasManyAssociation extends Association
{

    public function __construct(BaseModel $owner, string $name, string $inverse, string $through = null,
                                string $class = null, string $foreign_key = null)
    {
        parent::__construct($owner, $name, $inverse, $through, $class, $foreign_key);
        $this->type = Association::MANY_TYPE;

        if (empty($foreign_key)) {
            $this->foreign_key = Inflector::underscore($this->owner::model_name()) . "_id";
        } else {
            $this->foreign_key = $foreign_key;
        }
    }

    public function get(): mixed
    {
        $table_name = $this->class_name::table_name();
        $class_name = $this->class_name::model_name();

//        $associated_objects = App::$store->lookup(model_name: $class_name, attribute: $this->foreign_key,
//            value: $this->owner->id);
        $owner_id = $this->owner->id;
        $this->data = (new CollectionProxy(table_name: $table_name, model_name: $class_name))
            ->where(["$table_name.$this->foreign_key=$owner_id"]);
        return $this->data;
    }

    public function set(mixed $value) {

    }
}