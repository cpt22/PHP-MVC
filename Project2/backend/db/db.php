<?php

class DB
{
    public $connection = null;

    public function __construct()
    {
        $this->connect();
    }

    /**
     * @return void
     */
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

    /**
     * @param $query
     * @return mixed
     */
    public function query($query)
    {
        return $this->connection->query($query);
    }

    /**
     * @param string $table
     * @param array $fields
     * @param array $substitutions
     * @param string $joins
     * @param array $join_tables
     * @param $where_conditions
     * @param $where_operator
     * @param $limit
     * @return mixed
     */
    public function select(string $table, array $fields = [], array $substitutions = [], string $joins = "",
                           array $join_tables = [], $where_conditions = [], $where_operator = "AND", $limit = null)
    {
        if (empty($fields))
            $fields = "*";
        if (empty($joins) && !empty($join_tables))
            $joins = $this->generate_joins($join_tables);

        $where = $this->generate_where(conditions: $where_conditions, operator: $where_operator);

        $query = "SELECT $fields FROM $table $joins $where" . (isset($limit) ? " LIMIT $limit" : "") ;
        return $this->prepare($query, $substitutions);
    }

    /**
     * @param string $table
     * @param array $values
     * @return mixed
     */
    public function insert(string $table, array $values)
    {
        if (count($values) < 1)
            return false;
            //App::$logger->log_error(code: 500, message: "Incorrect number of arguments passed to insert method");

        $field_str = $this->process_fields(array_keys($values));
        $query = "INSERT INTO " . $table . " SET $field_str";
        return $this->prepare(query: $query, kvs: $values);
    }

    /**
     * @param string $table
     * @param array $fields
     * @param array $values
     * @param array $where_conditions
     * @param string $where_operator
     * @return mixed
     */
    public function update(string $table, array $fields, array $values, array $where_conditions = [],
                           string $where_operator = "AND")
    {
        if (count($fields) < 1 || count($values) < 1 || count($fields) != count($values))
            App::$logger->log_error(code: 500, message: "Incorrect number of arguments passed to update method");

        $where = $this->generate_where(conditions: $where_conditions, operator: $where_operator);

        $field_str = $this->process_fields($fields);
        $query = "UPDATE " . $table . " SET $field_str $where";
        return $this->prepare($query, kvs: $values);
    }

    /**
     * @param string $table
     * @param array $values
     * @param array $where_conditions
     * @param string $where_operator
     * @return mixed
     */
    public function delete(string $table, array $values = [], array $where_conditions = [], string $where_operator = "AND")
    {
        $where = $this->generate_where(conditions: $where_conditions, operator: $where_operator);
        $query = "DELETE FROM " . $table . " $where";
        return $this->prepare(query: $query, kvs: $values);
    }


    /**
     * @param string $query
     * @param array $kvs
     * @return mixed
     */
    public function prepare(string $query, array $kvs)
    {
        $vals = [];
        foreach ($kvs as $key => $value) {
            $vals[":$key"] = $value;
        }

        $statement = $this->connection->prepare($query);
        $statement->execute($kvs);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        return $statement;
    }


    /**
     * @param array $conditions
     * @param string $operator
     * @return string
     */
    private function generate_where(array $conditions, string $operator = "AND"): string
    {
        $conditions = array_map(fn($value): string => "( $value )", $conditions);
        $where = join(" $operator ", $conditions);
        return "WHERE " . $where;
    }

    /**
     * @param array $fields
     * @return string
     */
    private function process_fields(array $fields): string {
        $tmp = [];
        foreach($fields as $field) {
            $tmp[] = "$field=:$field";
        }
        return join(',', $tmp);
    }

    /**
     * @param array $join_tables
     * @return string
     */
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
