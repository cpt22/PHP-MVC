<?php

class ObjectStore
{
    private array $objects = [];

    public function __construct() {}

    public function store(string $model_name, int $id, object $object): void {
        if (!array_key_exists($model_name, $this->objects)) {
            $this->objects[$model_name] = [];
        }
        $this->objects[$model_name][$id] = $object;
    }

    public function retrieve(string $model_name, int $id): ?object {
        if (!array_key_exists($model_name, $this->objects)) { return null; }
        return $this->objects[$model_name][$id];
    }
}