<?php

abstract class Association
{
    const MANY_TYPE = "has_many";
    const ONE_TYPE = "has_one";
    const BELONGS_TYPE = "belongs_to";

    public BaseModel $owner;
    public string $type;
    public string $name;
    public string $inverse;
    public string $through;
    public string $class_name;
    public string $foreign_key;

    public mixed $data;

    public function __construct(BaseModel $owner, string $name, string $inverse, string $through = null, string $class = null, string $foreign_key = null) {
        $this->owner = $owner;
        $this->name = $name;
        $this->inverse = $inverse;
        $this->through = $through;
        if (empty($class)) {
            $this->class_name = Inflector::classify(Inflector::singularize($name));
        } else {
            $this->class_name = $class;
        }
    }

    public abstract function get(): mixed;
    public abstract function set(mixed $value);
//    public function get() {
//        $table_name = $this->class_name::table_name();
//        $class_name = $this->class_name::model_name();
//        $fk_name = Inflector::underscore($this->owner::model_name()) . "_id";
//        $owner_id = $this->owner->id;
//        $this->data = (new CollectionProxy(table_name: $table_name, model_name: $class_name))
//            ->where(["$fk_name=$owner_id"])->load_if_needed();
//        return $this->data;
//    }
}