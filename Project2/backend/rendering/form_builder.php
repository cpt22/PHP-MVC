<?php

class FormBuilder
{
    protected object $object;

    public function __construct(object $object) {
        $this->object = $object;
    }

    public function label(string $for, string $contents = null, string $class = null, string $field_id_override = null,
                          string $id = null, array $html_options = []) {
        // TODO: Finish implementing
        $field_id = $this->id_attr(field: $for, id: $field_id_override);
        $id = $id ?? ($field_id . "_label");
        $attributes = $this->parse_html_options($html_options);
        $attributes[] = "for=\"$field_id\"";
        if (!empty($class) || $this->field_has_error($for)) {
            $classes = [];
            if ($this->field_has_error($for)) { $classes[] = "is-invalid-label"; }
            if (!empty($class)) { $classes[] = $class; }
            $attributes[] = "class=\"" . join(' ', $classes) . "\"";
        }

        $contents = $contents ?? (Inflector::titleize($for) . ":");

        $attribute_string = join(' ', $attributes);
        echo "<label id=\"$id\" $attribute_string>$contents</label>";
    }

    /**
     * @param string $field
     * @param string|null $value
     * @param string|null $class
     * @param string|null $id
     * @param string|null $size
     * @param array $html_options
     * @return void
     */
    public function text_area(string $field, string $value = null, string $class = null, string $id = null,
                              string $size = null, array $html_options = []) {
        $extra_attrs = [];
        if (!empty($size)) {
            $splt = explode('x', $size);
            $extra_attrs[] = "rows=\"$splt[0]\" cols=\"$splt[1]\"";
        }

        $attribute_string = $this->generate_attributes(field: $field, value: $value, class: $class, id: $id,
            html_options: $html_options, additional_attributes: $extra_attrs);

        echo "<textarea $attribute_string></textarea>";
    }

    public function text_field(string $field, string $value = null, string $class = null, string $id = null,
                               string $maxlength = null, array $html_options = []) {
        $extra_attrs = [];
        if (!empty($maxlength)) {
            $extra_attrs[] = "maxlength=$maxlength";
        }

        $attribute_string = $this->generate_attributes(field: $field, value: $value, class: $class, id: $id,
            html_options: $html_options, additional_attributes: $extra_attrs);

        echo "<input type=\"text\" $attribute_string />";
    }

    private function generate_attributes(string $field, ?string $value, ?string $class, ?string $id, array $html_options,
                                         array $additional_attributes = [], bool $as_string = true): mixed
    {
        $attributes = [];
        $attributes[] = $this->class_attr($field, $class);
        $attributes[] = "id=\"" . $this->id_attr($field, $id). "\"";
        $attributes[] = $this->name_attr($field);
        $attributes[] = $this->value_attr($field, $value);
        $attributes = array_merge($attributes, $this->parse_html_options($html_options));
        $attributes = array_filter(array_merge($attributes, $additional_attributes));
        if ($as_string) { return join(' ', $attributes); }
        return $attributes;
    }

    private function value_attr(string $field, ?string $passed_value = null) {
        $val = "";
        if (empty($passed_value)) {
            $val = $this->object->{$field};
        } else {
            $val = $passed_value;
        }
        return "value=\"$val\"";
    }

    /**
     * @param array $options
     * @param string $prefix
     * @return array
     */
    private function parse_html_options(array $options, string $prefix = ""): array {
        $prefixed_name = function(string $name) use ($prefix): string
        {
            return empty($prefix) ? $name : join('_', [$prefix, $name]);
        };
        $attrs = [];
        foreach ($options as $name => $value) {
            if (is_null($value)) {
                $attrs[] = $prefixed_name($name);
            } else if (is_numeric($name)) {
                $attrs[] = $prefixed_name($value);
            } else if (is_array($value)) {
                print_r($prefix);
                $attrs = array_merge($attrs, $this->parse_html_options($value, $prefixed_name($name)));
            } else {
                $attrs[] = $prefixed_name($name) . "=\"$value\"";
            }
        }
        return $attrs;
    }

    private function class_attr(string $field, ?string $classes): string {
        $cls = [];
        if ($this->field_has_error($field)) { $cls[] = "is-invalid-field"; }
        if (!empty($classes)) { $cls[] = $classes; };
        if (empty($cls)) { return ""; }
        return "class=\"" . join(' ', $cls ) . "\"";
    }

    private function id_attr(string $field, string $id = null): string {
        return $id ?? $field . "_field";
    }

    private function name_attr(string $field): string {
        return "name=\"" . Inflector::underscore($this->object::model_name()) . "[$field]\"";
    }

    private function field_has_error(string $field): bool
    {
        return array_key_exists($field, $this->object->errors);
    }
}