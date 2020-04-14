<?php

require_once('model.php');

class Respuestas extends Model {

    private $table_name = "RESPUESTAS";
    // propiedades
    public $id;
    public $encresp;
    public $pregunta;
    public $opcion;

    public function createRespuesta() {
        $ssql = "INSERT INTO $this->table_name(IdEncresp,Opcion,Pregunta)
                VALUES (?,?,?);";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute(array(
            $this->encresp,
            $this->opcion,
            $this->pregunta
        ));
        return $this->conn->lastInsertId();
    }

}

?>