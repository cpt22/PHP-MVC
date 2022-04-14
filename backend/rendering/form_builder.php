<?php

class FormBuilder
{
    protected object $object;

    public function __construct(object $object) {
        $this->object = $object;
    }

    /**
     * @param string $for
     * @param string|null $contents
     * @param string|null $class
     * @param string|null $field_id_override
     * @param string|null $id
     * @param array $html_options
     * @return void
     */
    public function label(string $for, string $contents = null, string $class = null, string $field_id_override = null,
                          string $id = null, array $html_options = []) {
        // TODO: Finish implementing
        $field_id = $this->field_id(field: $for, id: $field_id_override);
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
                             string $maxlength = null, string $size = null, array $html_options = []) {
        $extra_attrs = [];
        if (!empty($size)) {
            $splt = explode('x', $size);
            $extra_attrs[] = "rows=\"$splt[0]\" cols=\"$splt[1]\"";
        }

        $attribute_string = $this->generate_attributes(field: $field, value: $value, class: $class, id: $id, maxlength: $maxlength,
            html_options: $html_options, additional_attributes: $extra_attrs);

        echo "<textarea $attribute_string>" . $this->field_value($field) . "</textarea>";
    }

    /**
     * @param string $field
     * @param string|null $value
     * @param string|null $class
     * @param string|null $id
     * @param string|null $maxlength
     * @param array $html_options
     * @return void
     */
    public function text_field(string $field, string $value = null, string $class = null, string $id = null,
                               string $maxlength = null, array $html_options = []) {
        $attribute_string = $this->generate_attributes(field: $field, value: $value, class: $class, id: $id, maxlength: $maxlength,
            html_options: $html_options);

        echo "<input type=\"text\" $attribute_string />";
    }

    /**
     * @param string $field
     * @param string|null $value
     * @param string|null $class
     * @param string|null $id
     * @param string|null $maxlength
     * @param array $html_options
     * @return void
     */
    public function password_field(string $field, string $value = null, string $class = null, string $id = null,
                               string $maxlength = null, array $html_options = []) {
        $attribute_string = $this->generate_attributes(field: $field, value: $value, class: $class, id: $id, maxlength: $maxlength,
            html_options: $html_options);

        echo "<input type=\"password\" $attribute_string />";
    }

    /**
     * @param string $field
     * @param string|null $value
     * @param string|null $class
     * @param string|null $id
     * @param string|null $maxlength
     * @param array $html_options
     * @return void
     */
    public function number_field(string $field, string $value = null, string $class = null, string $id = null,
                               string $maxlength = null, array $html_options = []) {
        $attribute_string = $this->generate_attributes(field: $field, value: $value, class: $class, id: $id, maxlength: $maxlength,
            html_options: $html_options);

        echo "<input type=\"number\" $attribute_string />";
    }

    /**
     * @return void
     */
    public function submit() {
        echo "<input type=\"submit\" name=\"submit\" value=\"submit\" />";
    }

    /**
     * @param string $field
     * @param string|null $value
     * @param string|null $class
     * @param string|null $id
     * @param array $html_options
     * @param array $additional_attributes
     * @param bool $as_string
     * @return mixed
     */
    private function generate_attributes(string $field, ?string $value, ?string $class, ?string $id, ?string $maxlength,
                                         array $html_options, array $additional_attributes = [], bool $as_string = true): mixed
    {
        $attributes = [];
        if ($class_attr = $this->field_classes($field, $class)) {
            $attributes[] = "class=\"$class_attr\"";
        }
        if ($id_attr = $this->field_id($field, $id)) {
            $attributes[] = "id=\"$id_attr\"";
        }
        if ($name_attr = $this->field_name($field)) {
            $attributes[] = "name=\"$name_attr\"";
        }
        if ($value_attr = $this->field_value($field, $value)) {
            $attributes[] = "value=\"$value_attr\"";
        }
        if (!empty($maxlength)) {
            $attributes[] = "maxlength=\"$maxlength\"";
        }
        $attributes = array_merge($attributes, $this->parse_html_options($html_options));
        $attributes = array_filter(array_merge($attributes, $additional_attributes));
        if ($as_string) { return join(' ', $attributes); }
        return $attributes;
    }

    /**
     * @param string $field
     * @param string|null $passed_value
     * @return string
     */
    private function field_value(string $field, ?string $passed_value = null) {
        $val = "";
        if (empty($passed_value)) {
            $val = $this->object->{$field};
        } else {
            $val = $passed_value;
        }
        return $val;
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
                $attrs = array_merge($attrs, $this->parse_html_options($value, $prefixed_name($name)));
            } else {
                $attrs[] = $prefixed_name($name) . "=\"$value\"";
            }
        }
        return $attrs;
    }

    /**
     * @param string $field
     * @param string|null $classes
     * @return string
     */
    private function field_classes(string $field, ?string $classes): string {
        $cls = [];
        if ($this->field_has_error($field)) { $cls[] = "is-invalid-field"; }
        if (!empty($classes)) { $cls[] = $classes; };
        if (empty($cls)) { return ""; }
        return join(' ', $cls );
    }

    /**
     * @param string $field
     * @param string|null $id
     * @return string
     */
    private function field_id(string $field, string $id = null): string {
        return $id ?? $field . "_field";
    }

    /**
     * @param string $field
     * @return string
     */
    private function field_name(string $field): string {
        return Inflector::underscore($this->object::model_name()) . "[$field]";
    }

    /**
     * @param string $field
     * @return bool
     */
    private function field_has_error(string $field): bool
    {
        return array_key_exists($field, $this->object->errors);
    }
}

/**
 * @param object $object
 * @param string|null $url
 * @param string $method
 * @param string|null $name
 * @param callable|null $do
 * @return void
 * @throws Exception
 */
function form_for(object $object, string $url = null, string $method = "post", string $name = null, callable $do = null)
{
    if ($object == null || $do == null) {
        throw new Exception("REPLACE THIS: Form object and callback must exist");
    }
    $form = new FormBuilder($object);
print_r($url);
    $method = strtolower($method);
    if ($method == "post" || $method == "get") {
        $real_method = $method;
    } else {
        $real_method = "post";
        $custom_method_html = "<input type=\"hidden\" name=\"REQUEST_METHOD\" value=\"$method\" />";
    }

    echo "<form action=\"$url\" accept-charset=\"UTF-8\" method=\"$real_method\">";
    if (isset($custom_method_html)) {
        echo $custom_method_html;
    }

    $do($form);

    echo "</form>";
}