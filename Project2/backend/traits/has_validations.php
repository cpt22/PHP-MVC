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
    }
}