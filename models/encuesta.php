<?php

require_once('model.php');

class Encuesta extends Model {

    private $table_name = "ENCUESTA";
    // propiedades
    public $id;
    public $titulo;
    public $instrucciones;

    public function getEncuestaAll() {
        $ssql = "SELECT IdEnc, Descripcion, Instrucciones 
                    FROM $this->table_name 
                WHERE IsDeleted=0 
                ORDER BY IdEnc;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEncuestaById($eid) {
        $ssql = "SELECT Descripcion, Instrucciones
                    FROM $this->table_name
                WHERE IsDeleted=0";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateEncuesta() {
        $ssql = "UPDATE $this->table_name 
                    SET Descripcion=?,Instrucciones=? 
                WHERE IdEnc=?;";
        $stmt = $this->conn->prepare($ssql);
        return $stmt->execute(array($this->titulo,
                             $this->instrucciones,
                             $this->id)
        );
    }

}

?>