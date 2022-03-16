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
}