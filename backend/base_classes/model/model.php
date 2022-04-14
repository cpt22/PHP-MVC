<?php
abstract class BaseModel
{
    use HasAssociations;
    use HasValidations;
    use HasCallbacks;

    protected bool $modified_field_tracking = true;

    protected array $modified_fields = [];
    private array $attributes = [];

    protected static ?array $db_fields = null;

    protected function __construct()
    {
        $this->setup();
    }

    public function __set($name, $val) {
        if ($this->is_association($name)) {
            $this->set_on_association($name, $val);
        } else if (self::is_field($name)) {
            $this->attributes[$name] = $val;
            if ($this->modified_field_tracking) {
                $this->modified_fields[$name] = $val;
            }
        }
        if (!empty($this->id)) { App::$store->store(self::model_name(), $this->id, $this); }
    }

    /**
     * @param $var
     * @return mixed|void
     */
    public function __get($var) {
        if ($this->is_association($var)) {
            return $this->get_association_objects($var);
        }
        if (array_key_exists($var, $this->attributes)) {
            return $this->attributes[$var];
        }
        if (isset($this->{$var})) {
            return $this->{$var};
        }
        debug_print_backtrace();
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name) {
        return array_key_exists($name, $this->attributes);
    }

    abstract protected function setup();

    private function pause_modified_field_tracking() { $this->modified_field_tracking = false; }
    private function resume_modified_field_tracking() {$this->modified_field_tracking = true; }


    /**
     * @return string
     * Gets the table name for this model.
     */
    public static function table_name(): string
    {
        return Inflector::tableize(get_called_class());
    }

    /**
     * @return string
     * Gets the name of the model.
     */
    public static function model_name(): string
    {
        return get_called_class();
    }

    /**
     * Loads the database fields for this model to enable saving and updating properly.
     * @return array
     */
    public static function get_db_fields(): array
    {
        if (self::$db_fields == null) {
            $query = "SELECT COLUMN_NAME AS cn FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'".self::table_name()."'";
            $result = App::$db->query($query);
            $fields = [];
            foreach ($result->fetchAll(mode: PDO::FETCH_ASSOC) as $row)
            {
                $field = $row['cn'];
                $fields[] = $field;
            }
            self::$db_fields = $fields;
            return self::$db_fields;
        }
        return self::$db_fields;
    }

    /**
     * @param string $field
     * @return bool
     */
    public static function is_field(string $field): bool {
        return in_array($field, self::get_db_fields());
    }

    public function load_from_attributes(array $attributes) {
        $this->pause_modified_field_tracking();
        foreach ($attributes as $attr => $value) {
            $this->{$attr} = $value;
        }
        $this->resume_modified_field_tracking();
    }

    /**
     * @return void
     */
    private function clear_modified_fields(): void {
        $this->modified_fields = [];
    }

    public function is_new_record(): bool {
        return empty($this->id);
    }

    // TODO: Fix return types and error handling

    /**
     * @return bool
     */
    public function save(bool $exception = false): bool
    {
        return $this->save_internal(exception: $exception);
    }

    /**
     * @param bool $exception
     * @return bool
     */
    protected function save_internal(bool $exception = false): bool
    {
        $is_new_record = $this->is_new_record();
        // TODO: handle exceptions for errors
        $this->run_validations();
        if (!empty($this->errors)) { return false; }
        if ($is_new_record) { $this->run_before_create(); } else { $this->run_before_update(); }
        $this->run_before_save();
        $values = array_intersect_key($this->attributes, array_flip(array_keys($this->modified_fields)), array_flip(self::get_db_fields()));
        try {
            if (count($this->modified_fields) > 0) {
                if ($is_new_record) {
                    App::$db->insert(table: self::table_name(), values: $values);
                    $this->attributes['id'] = App::$db->connection->lastInsertId();
                } else {
                    App::$db->update(table: self::table_name(), fields: array_keys($values), values: $values, where_conditions: array("id=$this->id"));
                }
                $this->clear_modified_fields();
            }
        } catch (PDOException $e) {
            if ($exception) { throw $e; }
            return false;
        }
        $this->run_after_save();
        if ($is_new_record) { $this->run_after_create(); } else { $this->run_after_update(); }
        return true;
    }

    /**
     * @param array $fields
     * @return void
     */
    public function update(array $fields, bool $exception = false) {
        $this->save_internal(exception: $exception);
    }

    /**
     * @return void
     */
    public function reload() {
        //TODO: Implement
    }

    /**
     * @return void
     */
    public function destroy(bool $exception = false) {
        $this->run_before_destroy();
        if (isset($this->id) && !empty($this->id)) {
            try {
                App::$db->delete(self::table_name(), values: ["id" => $this->id], where_conditions: ["id=:id"]);
                App::$store->unstore(self::model_name(), $this->id);
            } catch (PDOException $e) {
                if ($exception) { throw $e; }
                return false;
            }
        }
        $this->run_after_destroy();
        return true;
    }

    /**
     * @param mixed $value
     * @return mixed|null
     */
    public static function find(mixed $value, bool $exception = false): mixed
    {
        return self::find_by(value: $value, exception: $exception);
    }

    /**
     * @param mixed $value
     * @param string $attribute
     * @return mixed|object|null
     */
    public static function find_by(mixed $value, string $attribute = "id", bool $exception = false): mixed
    {
        if (is_array($value)) {
            return null;
        }

        if ($attribute == "id") {
            $obj = App::$store->retrieve(self::model_name(), $value);
            if ($obj != null) {
                return $obj;
            }
        }

        $result = App::$db->select(table: self::table_name(), substitutions: array("val" => $value),
            where_conditions: array("$attribute=:val"), limit: 1);
        if ($result->rowCount() == 1) {
            $data = $result->fetch();
            if (!empty($data['id'])) {
                $obj = App::$store->retrieve(self::model_name(), $data['id']);
                if ($obj != null) {
                    return $obj;
                }
            }
            $class_name = self::model_name();
            $obj = new $class_name();
            $obj->load_from_attributes($data);
            return $obj;
        }
        if ($exception) { throw new RecordNotFoundException(self::model_name() . " with $attribute=$value could not be found."); }
        return null;
    }


    /**
     * @param array $attributes
     * @param bool $exception
     * @return mixed
     */
    public static function create(array $attributes, bool $exception = false)
    {
        // Instantiate class
        $class_name = get_called_class();
        $class = new $class_name();

        $class->load_from_attributes($attributes);
        $class->save_internal($exception);
        return $class;
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public static function new(array $attributes = []): Model
    {
        $class_name = get_called_class();
        $class = new $class_name();
        // Assign attributes
        $class->load_from_attributes($attributes);
        return $class;
    }

    private static function make_proxy() { return new CollectionProxy(table_name: self::table_name(), model_name: self::model_name()); }
    public static function where(array $conditions, array $values) { return self::make_proxy()->where($conditions, $values); }
    public static function group(string $group) { return self::make_proxy()->group($group); }
    public static function order(string $order) { return self::make_proxy()->order($order); }
    public static function limit(int $limit) { return self::make_proxy()->limit($limit); }
    public static function includes($includes) { return self::make_proxy()->includes($includes); }
    public static function pluck(array $fields) { return self::make_proxy()->pluck($fields); }
    public static function count() { return self::make_proxy()->count(); }
    public static function all() { return self::make_proxy()->all; }
}
?>