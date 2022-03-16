<?php
trait HasCallbacks {
    protected array $callbacks = [
        'before_validation' => [],
        'after_validation' => [],
        'before_save' => [],
        'after_save' => [],
        'before_create' => [],
        'after_create' => [],
        'before_update' => [],
        'after_update' => [],
        'before_destroy' => [],
        'after_destroy' => []
    ];

    protected function before_validation(string $method, array $params = []) {}
    protected function after_validation(string $method, array $params = []) {}

    protected function before_save(string $method, array $params = []) {}
    protected function after_save(string $method, array $params = []) {}

    protected function before_create(string $method, array $params = []) {}
    protected function after_create(string $method, array $params = []) {}

    protected function before_update(string $method, array $params = []) {}
    protected function after_update(string $method, array $params = []) {}

    protected function before_destroy(string $method, array $params = []) {}
    protected function after_destroy(string $method, array $params = []) {}

    protected function run_before_validation() {}
    protected function run_after_validation() {}

    protected function run_before_save() {}
    protected function run_after_save() {}

    protected function run_before_create() {}
    protected function run_after_create() {}

    protected function run_before_update() {}
    protected function run_after_update() {}

    protected function run_before_destroy() {}
    protected function run_after_destroy() {}
}