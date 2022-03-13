<?php

class Association
{
    public string $type;
    public string $inverse;
    public string $through;
    public string $class;

    public function __construct(string $type, string $inverse, string $through, string $class) {
        $this->type = $type;
        $this->inverse = $inverse;
        $this->through = $through;
        $this->class = $class;
    }
}