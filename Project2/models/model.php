<?php
abstract class Model
{
    public int $id;
    private array $attributes;

    function save() {
        global $db;
        $values = array();
        foreach($this->attributes as $attr) {
            $values[] = $this->{$attr};
        }

        $attrs_as_s = join(", ", $this->attributes);
        $vals_placeholder = str_repeat(",?", count($attrs_as_s));
        $types = str_repeat("s", count($attrs_as_s));

        $table_name = strtolower(get_class($this)) . "s";

        $stmt = $db->prepare("UPDATE $table_name SET ($attrs_as_s) VALUES ($vals_placeholder) WHERE id=$this->id");
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $stmt->close();
    }

    function create() {
        global $db;
        $values = array();
        $tmp_attrs = $this->attributes;
        array_shift($tmp_attrs);
        foreach($tmp_attrs as $attr) {
            $values[] = $this->{$attr};
        }

        $attrs_as_s = join(", ", $tmp_attrs);
        $vals_placeholder = rtrim(str_repeat("?,", count($tmp_attrs)), ",");
        $types = str_repeat("s", count($tmp_attrs));

        $table_name = strtolower(get_class($this)) . "s";

        $query = "INSERT INTO $table_name ($attrs_as_s) VALUES ($vals_placeholder)";
        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $stmt->close();
    }
}
?>