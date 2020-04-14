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

    public function getCountAvgStdAllEncuestasFromAsig($asig) {
        $ssql = "SELECT RESPUESTAS.Pregunta, COUNT(OPCION.Texto),AVG(OPCION.Texto),STD(OPCION.Texto) FROM RESPUESTAS 
                 INNER JOIN OPCION ON OPCION.IdOp=RESPUESTAS.Opcion
                 WHERE RESPUESTAS.IdEncresp IN (SELECT ENCUESTA_RESP.IdEncresp FROM ENCUESTA_RESP WHERE ENCUESTA_RESP.Asignatura=?)
                 GROUP BY RESPUESTAS.Pregunta;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute(array($asig));
        return $stmt->fetchAll(PDO::FETCH_NUM);
    }

}

?>