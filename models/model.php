<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/SGEU/config/database.php');

class Model {

    //private $database = null;
    protected $conn = null;

    public function __construct(Database $db)
    {
        //$this->database = $db;
        $this->conn = $db->getConnection();
    }

    /*
    public function __destruct()
    {
        if ($this->database != null) {
            $this->database->__destruct();
            $this->database = null;
        }
    }
    */

}

?>