<?php

class Database {

    private $conn;  // PDO Object
    private $dsn = 'mysql:host=127.0.0.1;dbname=sgeu';
    private $user = 'pwusr';
    private $pass = 'clase2020';

    public function __construct()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO($this->dsn, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $th) {
            die($th->getMessage());
        }
    }

    public function getConnection() {
        // return PDO Object instantiated
        return $this->conn;
    }

    public function __destruct()
    {
        $this->conn = null;
    }
}

?>