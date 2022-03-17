<?php
class CollectionProxy implements ArrayAccess, Iterator {

    private string $table_name;
    private string $model_name;

    private int $pointer = 0;

    private array $wheres = [
        "conditions" => [],
        "values" => []
    ];

    private array $groups = [];
    private array $orders = [];
    private array $fields = [];
    private array $includes = [];
    private bool $count = false;
    private int $limit = -1;

    private bool $hasLoaded = false;
    private array $data = [];

    private string $query = "";

    private DB $db;

    public function __construct($table_name, $model_name)
    {
        $this->table_name = $table_name;
        $this->model_name = $model_name;
        $this->db = App::$db;
    }

    public function __toString(): string {
        return "Association Proxy Object: " . $this->query;
    }

    public function __get($key)
    {
        switch($key)
        {
            case "all":
                $this->construct_query();
                return $this;
            case "value":
            case "values":
                return $this->get_values();
            case "count":
                $obj = $this->count();
                return $obj->load()[0];;
        }
        $this->load_if_needed();
        return $this->data[$key];
    }

    public function __isset ($key) {
        return isset($this->data[$key]);
    }

    private function make_copy(): CollectionProxy {
        $copy = clone $this;
        $copy->data = [];
        $copy->hasLoaded = false;
        return $copy;
    }

    private function get_values(): mixed {
        $this->load_if_needed();
        if ($this->count) {
            return $this->data[0];
        }
        return $this->data;
    }



    public function where(array $conditions, array $values = []): CollectionProxy
    {
        $copy = $this->make_copy();
        $copy->wheres['conditions'] = array_merge($copy->wheres['conditions'], $conditions);
        $copy->wheres['values'] = array_merge($copy->wheres['values'], $values);
        $copy->construct_query();
        return $copy;
    }

    public function group(string $group): CollectionProxy
    {
        $copy = $this->make_copy();
        $copy->groups[] = $group;
        $copy->construct_query();
        return $copy;
    }

    public function order(string $order): CollectionProxy
    {
        $copy = $this->make_copy();
        $copy->orders[] = $order;
        $copy->construct_query();
        return $copy;
    }

    public function limit(int $limit): CollectionProxy
    {
        $copy = $this->make_copy();
        $copy->limit = $limit;
        $copy->construct_query();
        return $copy;
    }

    public function includes(array $models): CollectionProxy
    {
        $copy = $this->make_copy();
        foreach($models as $model)
        {
            $copy->includes[] = $model;
        }
        $copy->construct_query();
        return $copy;
    }

    public function pluck(array $fields): CollectionProxy
    {
        $copy = $this->make_copy();
        $copy->fields = array_merge($fields);
        $copy->construct_query();
        return $copy;
    }

    public function count(): CollectionProxy
    {
        $copy = $this->make_copy();
        $copy->count = true;
        $copy->construct_query();
        $copy->load_if_needed();
        return $copy;
    }

    public function load_if_needed(): CollectionProxy
    {
        if (!$this->hasLoaded) { $this->load_objects(); }
        return $this;
    }


    public function load(): CollectionProxy
    {
        $this->load_objects();
        return $this;
    }

    private function load_objects()
    {
        $query = $this->construct_query();
        $values = $this->wheres['values'];
        $result = $this->db->prepare($query, $values);
        $result->setFetchMode(PDO::FETCH_ASSOC);

        switch($this->get_load_mode())
        {
            case "class":
                $result->setFetchMode(PDO::FETCH_CLASS, $this->model_name);
                $this->data = $result->fetchAll();
                foreach ($this->data as $object)
                {
                    App::$store->store($this->model_name, $object->id, $object);
                }
                break;
            case "assoc":
                $this->data = $result->fetchAll();
                break;
            case "arr":
                $this->data = array_map(fn($row) => $row[$this->fields[0]], $result->fetchAll());
                break;
            case "count":
                $this->data = [array_values($result->fetchAll()[0])[0]];
                break;
        }
        $this->hasLoaded = true;
    }

    private function get_load_mode(): string
    {
        if ($this->count) { return "count"; }
        if (empty($this->fields)) { return "class"; }
        if (count($this->fields) == 1) { return "arr"; }
        return "assoc";
    }

    public function construct_query(): string
    {
        $this->hasLoaded = false;
        // Create fields
        $fields = "";
        $tmp_fields = array_map(fn($val) => "$this->table_name.$val", $this->fields);
        if ($this->count)
        {
            $fields = "COUNT(*)";
        } else {
            if (empty($tmp_fields))
            {
                $fields = "*";
            } else {
                $fields = join(',', $tmp_fields);
            }
        }

        $model_tables = [];
        foreach ($this->includes as $include)
        {
            $model_tables[$include] = call_user_func(ucfirst($include) . '::table_name');
        }
        $tables = $this->table_name;
        foreach ($model_tables as $model => $table)
        {
            $tables .= " INNER JOIN $table ON $this->table_name.{$model}_id=$table.id";
        }

        $where = "";
        if (!empty($this->wheres['conditions']))
        {
            $where = "WHERE " . join(' AND ', $this->wheres['conditions']);
        }

        $group = "";
        if (!empty($this->groups))
        {
            $group = "GROUP BY " . join(',', $this->groups);
        }

        $order = "";
        if (!empty($this->orders))
        {
            $order = "ORDER BY " . join(',', $this->orders);
        }

        $limit = "";
        if ($this->limit > 0) {
           $limit = "LIMIT $this->limit";
        }

        $this->query = "SELECT $fields FROM $tables $where $group $order $limit";
        return $this->query;
    }


    /**
     * Array access methods
     */
    public function offsetExists($offset) {
        $this->load_if_needed();
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        $this->load_if_needed();
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }

    public function offsetSet($offset,$value) {
        $this->load_if_needed();
        if (is_null($offset)) {
           $this->data[] = $value;
        } else {
           $this->data[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        $this->load_if_needed();
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }

    /**
     * Iterator Methods
     */
    public function key() {
        $this->load_if_needed();
        return $this->pointer;
    }

    public function current() {
        $this->load_if_needed();
        return $this->data[$this->pointer];
    }

    public function next() {
        $this->load_if_needed();
        $this->pointer++;
    }

    public function rewind() {
        $this->load_if_needed();
        $this->pointer = 0;
    }

    public function seek($position) {
        $this->load_if_needed();
        $this->pointer = $position;
    }

    public function valid() {
        $this->load_if_needed();
        return isset($this->data[$this->pointer]);
    }
}