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

    protected function before_validation() {}
    protected function after_validation() {}

    protected function before_save() {}
    protected function after_save() {}

    protected function before_create() {}
    protected function after_create() {}

    protected function before_update() {}
    protected function after_update() {}

    protected function before_destroy() {}
    protected function after_destroy() {}

    protected function run_before_validation() {}
    protected function run_after_validation() {}

    protected function run_before_save() {}
    protected function run_after_save() {}

    protected function run_before_update() {}
    protected function run_after_update() {}

    protected function run_before_destroy() {}
    protected function run_after_destroy() {}
}