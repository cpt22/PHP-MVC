<?php
class AssociationProxy implements ArrayAccess {

    private string $table_name;
    private string $model_name;

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
    private array $objects = [];

    private string $query = "";

    public function __construct($table_name, $model_name)
    {
        $this->table_name = $table_name;
        $this->model_name = $model_name;
    }

    public function __toString(): string {
        return "Association Proxy Object: " . $this->query;
    }

    public function __call($name, $arguments)
    {
        echo "Calling object method '$name' "
            . implode(', ', $arguments). "\n";
    }

    public function __get($key)
    {
        echo "Getting property '$key'";
        switch($key)
        {
            case "count":
                $this->count = true;
                $this->construct_query();
                return;
        }
        if (!$this->hasLoaded)
        {
            $this->hasLoaded = true;
            $this->load_objects();
        }
        return $this->objects[$key];
    }

    public function __isset ($key) {
        return isset($this->objects[$key]);
    }

    public function where(array $conditions, array $values): AssociationProxy
    {
        $this->wheres['conditions'] = array_merge($this->wheres['conditions'], $conditions);
        $this->wheres['values'] = array_merge($this->wheres['values'], $values);
        $this->construct_query();
        return $this;
    }

    public function group(string $group): AssociationProxy
    {
        $this->groups[] = $group;
        $this->construct_query();
        return $this;
    }

    public function order(string $order): AssociationProxy
    {
        $this->orders[] = $order;
        $this->construct_query();
        return $this;
    }

    public function limit(int $limit): AssociationProxy
    {
        $this->limit = $limit;
        $this->construct_query();
        return $this;
    }

    public function includes(array $models): AssociationProxy
    {
        foreach($models as $model)
        {
            $this->includes[] = $model;
        }
        $this->construct_query();
        return $this;
    }

    public function pluck($fields): AssociationProxy
    {
        $this->fields = $fields;
        $this->construct_query();
        return $this;
    }

    private function load_objects() {
        global $db, $store;
        $query = $this->construct_query();
        $values = $this->wheres['values'];
        $result = $db->prepare($query, $values);
        $result->setFetchMode(PDO::FETCH_CLASS, $this->model_name);
        $this->objects = $result->fetchAll();
        foreach ($this->objects as $object) {
            $store->store($this->model_name, $object->id, $object);
        }
    }

    public function construct_query(): String
    {
        // Create fields
        $fields = "";
        if ($this->count)
        {
            $fields = "COUNT(*)";
        } else {
            if (empty($this->fields))
            {
                $fields = "*";
            } else {
                $fields = join(',', $this->fields);
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
        if ($this->limit != -1) {
           $limit = "LIMIT $this->limit";
        }

        $this->query = "SELECT $fields FROM $tables $where $group $order $limit";
        return $this->query;
    }


    /*
     * Array access methods
     */
    public function offsetExists($offset) {
        return isset($this->objects[$offset]);
    }

    public function offsetGet($offset) {
        if (!$this->hasLoaded)
        {
            $this->hasLoaded = true;
            $this->load_objects();
        }
        return $this->offsetExists($offset) ? $this->objects[$offset] : null;
    }

    public function offsetSet($offset,$value) {
        if (is_null($offset)) {
           $this->objects[] = $value;
        } else {
           $this->objects[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        if ($this->offsetExists($offset)) {
            unset($this->objects[$offset]);
        }
    }
}