<?php
abstract class Model
{
    use HasAssociations;

    public int $id;

    function __construct()
    {
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

    public static function get_attributes(): array
    {
        global $db;
        $query = "SELECT COLUMN_NAME AS cn FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'".self::table_name()."'";
        $result = $db->query($query);
        $fields = array();
        foreach ($result->fetchAll(mode: PDO::FETCH_ASSOC) as $row)
        {
            $field = $row['cn'];
            $fields[] = $field;
        }
        return $fields;
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
            $this->id = $db->connection->lastInsertId();
            return true;
        }

        $result = $db->update(table: self::table_name(), fields: $this->db_fields, values: $values, where_conditions: array("id=$this->id"));
        return true;
    }

    public static function find(mixed $value, string $attribute = "id")
    {
        global $db;
        if (is_array($value)) {
            return null;
        }

        $result = $db->select(table: self::table_name(), substitutions: array("val" => $value),
            where_conditions: array("$attribute=:val"), limit: 1);
        $class_name = self::model_name();
        $obj = new $class_name();
        foreach ($result->fetch(mode: PDO::FETCH_ASSOC) as $column => $value) {
            $obj->{$column} = $value;
        }
        return $obj;
    }


    public static function create(array $attributes)
    {
        global $db;

        // Instantiate class
        $class_name = self::model_name();
        $class = new $class_name();

        foreach ($attributes as $attr => $value) {
            $class->{$attr} = $value;
        }
        $result = $db->insert(table: self::table_name(), values: $attributes);

        $class->id = $db->connection->lastInsertId();
        return $class;
    }


    private static function make_proxy() { return new AssociationProxy(table_name: self::table_name(), model_name: self::model_name()); }
    public static function where(array $conditions, array $values) { return self::make_proxy()->where($conditions, $values); }
    public static function group(string $group) { return self::make_proxy()->group($group); }
    public static function order(string $order) { return self::make_proxy()->order($order); }
    public static function limit(int $limit) { return self::make_proxy()->limit($limit); }
    public static function includes($includes) { return self::make_proxy()->includes($includes); }
    public static function pluck($fields) { return self::make_proxy()->pluck($fields); }
    public static function all() { return self::make_proxy()->all; }
}
?>