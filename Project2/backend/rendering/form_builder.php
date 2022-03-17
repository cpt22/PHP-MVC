<?php

class FormBuilder
{
    protected object $object;

    public function __construct(object $object) {
        $this->object = $object;
    }

    public function label(string $for, string $contents = null, string $class = null, string $field_id_override = null,
                          string $id = null, array $html_options = []) {
        $field_id = $this->id_for(field: $for, id: $field_id_override);
        $id = $id ?? ($field_id . "_label");
        $attributes = [];
        $attributes[] = "for=\"$field_id\"";
        if (!empty($class) || $this->field_has_error($for)) {
            $classes = [];
            if ($this->field_has_error($for)) { $classes[] = "label-invalid"; }
            if (!empty($class)) { $classes[] = $class; }
            $attributes[] = "class=\"" . join(' ', $classes . "\"");
        }

        $contents = $contents ?? (Inflector::titleize($for) . ":");

        $attribute_string = join(' ', $attributes);
        echo "<label id=\"$id\" $attribute_string>$contents</label>";
    }

    public function text_area(string $field, string $value = null, string $class = null, string $id = null,
                              string $size = null, array $html_options = []) {
    }

    private function id_for(string $field, string $id = null): string {
        return $id ?? $field . "_field";
    }

    private function field_has_error(string $field): bool
    {
        return array_key_exists($field, $this->object->errors);
    }
}