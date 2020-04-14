<?php

require_once('model.php');

class Profesor extends Model {

    private $table_name = "PROFESOR";
    // propiedades
    public $id;
    public $nombre;
    public $apellidos;

    // obtener todos los profesores de 
    // de una asignatura
    public function getProfesorByAsig($tit, $asig) {
        $ssql = "SELECT CodProf, NombreProf, ApellidosProf 
                 FROM PROFESOR WHERE CodProf IN 
                 (
                    SELECT CodProf FROM PROFESOR_ASIGNATURA 
                    WHERE CodTit=:tit AND CodAsig=:asig
                );";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute(array(
            "tit" => $tit,
            "asig" => $asig
        ));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>