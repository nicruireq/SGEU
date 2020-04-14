<?php

require_once('model.php');

class Encuesta_resp extends Model {

    private $table_name = "ENCUESTA_RESP";
    // propiedades
    public $id;
    public $titulacion;
    public $asignatura;
    public $encuesta;
    public $grupo;
    public $profesor;

    public function createEncuestaResp() {
        $ssql = "INSERT INTO $this->table_name(Titulacion,Asignatura,Encuesta,Grupo,ProfesorEvaluado)
                 VALUES (?,?,?,?,?);";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute(array(
            $this->titulacion,
            $this->asignatura,
            $this->encuesta,
            $this->grupo,
            $this->profesor
        ));
        return $this->conn->lastInsertId();
    }

}

?>