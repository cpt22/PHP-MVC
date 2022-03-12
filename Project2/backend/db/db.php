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
            $this->connection = new PDO($dsn, $user, $password);
            if ($pdo) {
                echo "Connected to the $db database successfully!";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $this->connection = mysqli_connect($DB_HOSTNAME, $DB_USER, $DB_PASSWORD, $DB_DB);
        if($this->connection->connect_error) {
            exit('Error connecting to database');
        }
        $this->connection->set_charset("utf8mb4");
    }




    public function query($query)
    {
        return $this->connection->query($query);
    }



    public function select(string $table, array $fields = array(), array $substitutions = array(), string $joins = "",
                           array $join_tables = array(), $where_conditions = array(), $where_operator = "AND")
    {
        global $logger;
        if (empty($fields))
            $fields = "*";
        if (empty($joins) && !empty($join_tables))
            $joins = $this->generate_joins($join_tables);

        $where = $this->generate_where(conditions: $where_conditions, operator: $where_operator);

        $query = "SELECT $fields FROM $table $joins $where";
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



    public function update(string $table, array $fields, array $values, array $where_conditions = array(),
                           string $where_operator = "AND")
    {
        global $logger;
        if (count($fields) < 1 || count($values) < 1 || count($fields) != count($values))
            $logger->log_error(code: 500, message: "Incorrect number of arguments passed to update method");

        $where = $this->generate_where(conditions: $where_conditions, operator: $where_operator);

        $field_str = $this->process_fields($fields);
        $query = "UPDATE " . $table . " SET $field_str $where";
        return $this->prepare($query, kvs: $values);
    }



    public function delete(string $table, array $values = array(), array $where_conditions = array(), string $where_operator = "AND")
    {
        global $logger;
        $where = $this->generate_where(conditions: $where_conditions, operator: $where_operator);
        $query = "DELETE FROM " . $table . " $where";
        return $this->prepare(query: $query, kvs: $values);
    }



    public function prepare(string $query, array $kvs)
    {
        // Extract all replacement keys and order their valuse
        $symbols = null;
        preg_match_all("/:+[a-zA-Z0-9]*/", $query, $symbols);
        $query = preg_replace("/:+[a-zA-Z0-9]*/", "?", $query);
        $values = array();
        foreach ($symbols[0] as $symbol)
        {
            $values[] = $kvs[substr($symbol, 1)];
        }

        $types = str_repeat("s", count($values));
        $stmt = $this->connection->prepare($query) or die(mysqli_error($this->connection));
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        print_r($stmt);
        return array("result" => $stmt->get_result(), "stmt" => $stmt);
    }



    private function generate_where(array $conditions, string $operator = "AND"): string
    {
        $conditions = array_map(fn($value): string => "( $value )", $conditions);
        $where = join(" $operator ", $conditions);
        return "WHERE " . $where;
    }

    private function process_fields(array $fields): string {
        $tmp = array();
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
