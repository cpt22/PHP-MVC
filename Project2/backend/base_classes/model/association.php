<?php

class Association
{
    const MANY_TYPE = "many";
    const ONE_TYPE = "one";
    const BELONGS_TYPE = "belongs";

    public string $type;
    public string $name;
    public string $inverse;
    public string $through;
    public string $class;

    public function __construct(string $type, string $name, string $inverse = "", string $through = "", string $class = "") {
        $this->type = $type;
        $this->name = $name;
        $this->inverse = $inverse;
        $this->through = $through;
        if (empty($class)) {
            $this->class = Inflector::singularize($name);
        } else {
            $this->class = $class;
        }
    }
}