<?php

class ObjectStore
{
    private array $objects = [];

    public function __construct() {}

    public function store(string $model_name, mixed $id, object $object): void {
        if (!array_key_exists($model_name, $this->objects)) {
            $this->objects[$model_name] = [];
        }
        $this->objects[$model_name][$id] = $object;
    }

    public function unstore(string $model_name, mixed $id): void {
        if (array_key_exists($model_name, $this->objects)) {
            unset($this->objects[$model_name][$id]);
        }
    }

    public function retrieve(string $model_name, mixed $id): ?object {
        if (!array_key_exists($model_name, $this->objects)) { return null; }
        if (!array_key_exists($id, $this->objects[$model_name])) { return null; }
        return $this->objects[$model_name][$id];
    }

    public function lookup(string $model_name, string $attribute, string $value, bool $singular = false): mixed {
        if (!array_key_exists($model_name, $this->objects)) { return null; }
        $to_return = [];
        foreach ($this->objects[$model_name] as $object) {
            if (isset($object->{$attribute}) && $object->{$attribute} == $value) {
                $to_return[] = $object;
            }
        }

        if ($singular) {
            if (count($to_return) > 0)
                return $to_return[0];
            return null;
        }
        return $to_return;
    }
}