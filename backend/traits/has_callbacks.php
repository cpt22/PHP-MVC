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

    protected function before_validation(string $method, array $params = []) {
        $this->callbacks['before_validation'][] = $method;
    }
    protected function after_validation(string $method, array $params = []) {
        $this->callbacks['after_validation'][] = $method;
    }

    protected function before_save(string $method, array $params = []) {
        $this->callbacks['before_save'][] = $method;
    }
    protected function after_save(string $method, array $params = []) {
        $this->callbacks['after_save'][] = $method;
    }

    protected function before_create(string $method, array $params = []) {
        $this->callbacks['before_create'][] = $method;
    }
    protected function after_create(string $method, array $params = []) {
        $this->callbacks['after_create'][] = $method;
    }

    protected function before_update(string $method, array $params = []) {
        $this->callbacks['before_update'][] = $method;
    }
    protected function after_update(string $method, array $params = []) {
        $this->callbacks['after_update'][] = $method;
    }

    protected function before_destroy(string $method, array $params = []) {
        $this->callbacks['before_destroy'][] = $method;
    }
    protected function after_destroy(string $method, array $params = []) {
        $this->callbacks['after_destroy'][] = $method;
    }

    protected function run_before_validation() {
        $this->run_callback_list('before_validation');
    }
    protected function run_after_validation() {
        $this->run_callback_list('after_validation');
    }

    protected function run_before_save() {
        $this->run_callback_list('before_save');
    }
    protected function run_after_save() {
        $this->run_callback_list('after_save');
    }

    protected function run_before_create() {
        $this->run_callback_list('before_create');
    }
    protected function run_after_create() {
        $this->run_callback_list('after_create');
    }

    protected function run_before_update() {
        $this->run_callback_list('before_update');
    }
    protected function run_after_update() {
        $this->run_callback_list('after_update');
    }

    protected function run_before_destroy() {
        $this->run_callback_list('before_destroy');
    }
    protected function run_after_destroy() {
        $this->run_callback_list('after_destroy');
    }

    protected function run_callback_list(string $callback_type) {
        if (array_key_exists($callback_type, $this->callbacks)) {
            foreach ($this->callbacks[$callback_type] as $callback) {
                $this->{$callback}();
            }
        }
    }
}