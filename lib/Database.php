<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/../config/config.php');

class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $pdo;
    private $error;

    public function __construct() {
        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
        $this->dbname = DB_NAME;
        
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("Connection failed: Please check the error log.");
        }
    }
    public function getPDO() {
        return $this->pdo;
    }
    public function execute($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    public function prepare($query) {
        return $this->pdo->prepare($query);
    }
    public function fetch($query, $params = []) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // public function execute($stmt) {
    //     return $stmt->execute();
    // }

    public function insert($query) {
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute();
    }

    public function select($query) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function delete($query) {
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute();
    }

    public function sanitize($input) {
        return htmlspecialchars(strip_tags($input));
    }

    public function beginTransaction() {
        $this->pdo->beginTransaction();
    }

    public function commit() {
        $this->pdo->commit();
    }

    public function rollBack() {
        $this->pdo->rollBack();
    }

    public function bindParam($stmt, $param, $value, $type = PDO::PARAM_STR) {
        $stmt->bindValue($param, $value, $type);
    }



    public function call($procedureName, $params = []) {
        // Create placeholders for parameters dynamically
        $placeholders = implode(', ', array_fill(0, count($params), '?'));
        
        // Prepare the stored procedure call
        $sql = "CALL $procedureName($placeholders)";
        $stmt = $this->pdo->prepare($sql);

        // Bind dynamic parameters to the statement
        foreach ($params as $index => $value) {
            // PDO::PARAM_STR as default, but it can be adjusted based on the type of the value
            $paramType = $this->getParamType($value);
            $stmt->bindValue($index + 1, $value, $paramType);
        }

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch results if needed
        }

        return false;
    }
    private function getParamType($value) {
        switch (true) {
            case is_int($value):
                return PDO::PARAM_INT;
            case is_bool($value):
                return PDO::PARAM_BOOL;
            case is_null($value):
                return PDO::PARAM_NULL;
            default:
                return PDO::PARAM_STR;
        }
    }
}
?>
