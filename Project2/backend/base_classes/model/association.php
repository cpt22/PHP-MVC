<?php

class Association
{
    const MANY_TYPE = "many";
    const ONE_TYPE = "one";
    const BELONGS_TYPE = "belongs";

    public BaseModel $owner;
    public string $type;
    public string $name;
    public string $inverse;
    public string $through;
    public string $class_name;

    public mixed $data;

    public function __construct(BaseModel $owner, string $type, string $name, string $inverse = "", string $through = "", string $class = "") {
        $this->owner = $owner;
        $this->type = $type;
        $this->name = $name;
        $this->inverse = $inverse;
        $this->through = $through;
        if (empty($class)) {
            $this->class_name = Inflector::classify(Inflector::singularize($name));
        } else {
            $this->class_name = $class;
        }

    }

    public function get() {
        $table_name = $this->class_name::table_name();
        $class_name = $this->class_name::model_name();
        $fk_name = Inflector::underscore($this->owner::model_name()) . "_id";
        $owner_id = $this->owner->id;
        $this->data = (new CollectionProxy(table_name: $table_name, model_name: $class_name))
            ->where(["$fk_name=$owner_id"])->load_if_needed();
        return $this->data;
    }
}