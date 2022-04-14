<?php

class BelongsToAssociation extends Association
{

    public function __construct(BaseModel $owner, string $name, string $inverse = "", string $through = null,
                                string $class = null, string $foreign_key = null)
    {
        parent::__construct($owner, $name, $inverse, $through, $class, $foreign_key);
        $this->type = Association::BELONGS_TYPE;

        if (empty($foreign_key)) {
            $this->foreign_key = Inflector::underscore($this->class_name::model_name()) . "_id";
        } else {
            $this->foreign_key = $foreign_key;
        }
    }
    // $this->belongs_to("user", inverse_of: "products");
    public function get(): mixed
    {
        if (!empty($this->data))
            return $this->data;

        $model_name = $this->class_name::model_name();
        $associated_id = $this->owner->{$this->foreign_key};
        $associated_obj = App::$store->retrieve($model_name, $associated_id);
        if ($associated_obj == null) {
            $associated_obj = $model_name::find($associated_id);
        }
        $this->data = $associated_obj;
        return $this->data;
    }

    public function set(mixed $value) {
        $this->data = $value;
    }
}