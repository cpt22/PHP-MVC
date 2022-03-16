<?php
trait HasValidations {
    protected array $errors = [];
    protected array $validations = [
    ];

    protected function validate(string $method) {
        $this->validations[] = ['type' => 'method', 'method' => $method];
    }

    protected function validates(string $field, array $validations) {
        if (empty($validations)) { return; }
        $this->validations[] = ['type' => 'field', 'field' => $field, 'validations' => $validations];
    }

    protected function run_validations() {
        $this->run_before_validation();
        foreach ($this->validations as $validation_item) {
            if ($validation_item['type'] == "method") {
                $this->{$validation_item['method']}();
            } else {
                $field = $validation_item['field'];
                foreach ($validation_item['validations'] as $name => $options) {
                    // TODO: Validators
                    $this->{"validation_method_" . $name}($field, $options);
                }
            }
        }
        $this->run_after_validation();
    }

    protected function add_error(string $field, string $message) {
        if (!array_key_exists($field, $this->errors)) { $this->errors[$field] = []; }
        $this->errors[$field][] = $message ?? "$field has an error.";
    }

    protected function validation_method_presence(string $field, mixed $options) {
        if (is_bool($options)) {
            if ($options == empty($this->{$field})) {
                $this->add_error($field, "$field must " . ($options ? "" : "not ") . "be present.");
            }
        }
    }
}