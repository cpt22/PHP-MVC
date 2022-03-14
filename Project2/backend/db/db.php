<?php

class DB
{
    public $connection = null;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        require_once $GLOBALS['APP_BASE'] . "db/dbconfig.php";
        $dsn = "mysql:host=$DB_HOSTNAME;dbname=$DB_DB;charset=UTF8";
        try {
            $this->connection = new PDO($dsn, $DB_USER, $DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }




    public function query($query)
    {
        return $this->connection->query($query);
    }



    public function select(string $table, array $fields = [], array $substitutions = [], string $joins = "",
                           array $join_tables = [], $where_conditions = [], $where_operator = "AND", $limit = null)
    {
        global $logger;
        if (empty($fields))
            $fields = "*";
        if (empty($joins) && !empty($join_tables))
            $joins = $this->generate_joins($join_tables);

        $where = $this->generate_where(conditions: $where_conditions, operator: $where_operator);

        $query = "SELECT $fields FROM $table $joins $where" . (isset($limit) ? " LIMIT $limit" : "") ;
        return $this->prepare($query, $substitutions);
    }




    public function insert(string $table, array $values)
    {
        global $logger;
        if (count($values) < 1)
            $logger->log_error(code: 500, message: "Incorrect number of arguments passed to insert method");

        $field_str = $this->process_fields(array_keys($values));
        $query = "INSERT INTO " . $table . " SET $field_str";
        return $this->prepare(query: $query, kvs: $values);
    }



    public function update(string $table, array $fields, array $values, array $where_conditions = [],
                           string $where_operator = "AND")
    {
        global $logger;
        if (count($fields) < 1 || count($values) < 1 || count($fields) != count($values))
            $logger->log_error(code: 500, message: "Incorrect number of arguments passed to update method");

        $where = $this->generate_where(conditions: $where_conditions, operator: $where_operator);

        $field_str = $this->process_fields($fields);
        $query = "UPDATE " . $table . " SET $field_str $where";
        echo $query;
        return $this->prepare($query, kvs: $values);
    }



    public function delete(string $table, array $values = [], array $where_conditions = [], string $where_operator = "AND")
    {
        global $logger;
        $where = $this->generate_where(conditions: $where_conditions, operator: $where_operator);
        $query = "DELETE FROM " . $table . " $where";
        return $this->prepare(query: $query, kvs: $values);
    }



    public function prepare(string $query, array $kvs)
    {
        $vals = [];
        foreach ($kvs as $key => $value) {
            $vals[":$key"] = $value;
        }

        $statement = $this->connection->prepare($query);
        $statement->execute($kvs);
        return $statement;
    }



    private function generate_where(array $conditions, string $operator = "AND"): string
    {
        $conditions = array_map(fn($value): string => "( $value )", $conditions);
        $where = join(" $operator ", $conditions);
        return "WHERE " . $where;
    }

    private function process_fields(array $fields): string {
        $tmp = [];
        foreach($fields as $field) {
            $tmp[] = "$field=:$field";
        }
        return join(',', $tmp);
    }

    private function generate_joins(array $join_tables): string
    {
        $joins = "";
        foreach ($join_tables as $table)
        {
            $joins .= ", $table";
        }
        return $joins;
    }
}
