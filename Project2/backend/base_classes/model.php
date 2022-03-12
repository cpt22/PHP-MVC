<?php
abstract class Model
{
    public $id;
    protected array $db_fields = array();

    function __construct(int $id = null)
    {
        $this->id = $id;
        $this->load_attributes();
        if ($this->id != null) {
            $this->load();
        } else {
        }
    }

    /**
     * @return string
     * Gets the table name for this model.
     */
    public static function table_name(): string
    {
        return strtolower(get_called_class()) . "s";
    }

    /**
     * @return string
     * Gets the name of the model.
     */
    public static function model_name(): string
    {
        return get_called_class();
    }



//    public function load_attributes()
//    {
//        $result = query("SELECT COLUMN_NAME AS cn FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'".$this->table_name()."'");
//        $fields = array("id");
//        while ($row = $result->fetch_assoc()) {
//            $field = $row['COLUMN_NAME'];
//            if ($field != "id") {
//                $this->{$field} = null;
//                $fields[] = $field;
//            }
//        }
//        $this->fields = $fields;
//    }

//    function load_values() {
//
//    }

//    function save()
//    {
//        global $db;
//        $values = array();
//        foreach($this->attributes as $attr)
//        {
//            $values[] = $this->{$attr};
//        }
//
//        $attrs_as_s = join(", ", $this->attributes);
//        $vals_placeholder = str_repeat(",?", count($attrs_as_s));
//        $types = str_repeat("s", count($attrs_as_s));
//
//        $table_name = table_name();
//
//        $stmt = $db->prepare("UPDATE $table_name SET ($attrs_as_s) VALUES ($vals_placeholder) WHERE id=$this->id");
//        $stmt->bind_param($types, ...$values);
//        $stmt->execute();
//        $stmt->close();
//    }


    private function load()
    {

    }


    public function load_attributes()
    {
        global $db;
        $query = "SELECT COLUMN_NAME AS cn FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'".self::table_name()."'";
        $result = $db->query($query);
        $fields = array();
        while ($row = $result->fetch_assoc())
        {
            $field = $row['cn'];
            if ($field != "id")
            {
                $this->{$field} = null;
                $fields[] = $field;
            }
        }
        $this->db_fields = $fields;
    }

    // TODO: Fix return types and error handling
    public function save()
    {
        global $db;
        $values = array();
        foreach ($this->db_fields as $field)
        {
            $values[$field] = $this->{$field};
        }

        if (empty($this->id))
        {
            $result = $db->insert(table: self::table_name(), values: $values);
            $this->id = $result['stmt']->insert_id;
            print_r($result);
            return true;
        }

        $result = $db->update(table: self::table_name(), fields: $this->db_fields, values: $values, where_conditions: array("id=$this->id"));
        print_r($result);
        return true;

    }

    public static function find(mixed $value, string $attribute = "id")
    {
        global $db;
        if (is_array($value))
        {
            return null;
        }

        $result = $db->select(table: self::table_name(), substitutions: array("attr" => $attribute, "val" => $value),
            where_conditions: array(":attr=:val"));
        print_r($result['result']);
    }

    //public function select(string $table, array $fields = array(), array $substitutions = array(), string $joins = "",
    //                           array $join_tables = array(), $where_conditions = array(), $where_operator = "AND")

    public static function create(array $attributes)
    {
        global $db;

        // Instantiate class
        $class_name = get_called_class();
        $class = new $class_name();

        foreach ($attributes as $attr => $value) {
            $class->{$attr} = $value;
        }
        $result = $db->insert(table: self::table_name(), values: $attributes);

        $class->id = $result['stmt']->insert_id;
        return $class;
    }
}
?>