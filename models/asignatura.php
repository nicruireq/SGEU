<?php

require_once('model.php');

class Asignatura extends Model {

    private $table_name = "ASIGNATURA";
    // propiedades
    public $titulacion;
    public $id;
    public $nombre;
    public $grupos;

    public function getAsignaturaByTit() {
        $ssql = "SELECT Tit, CodAsig, NombreAsig, NumGrupos 
                    FROM $this->table_name 
                WHERE Tit=?
                ORDER BY NombreAsig;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute(array($this->titulacion));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAsignaturaNombreById() {
        $ssql = "SELECT NombreAsig 
                    FROM $this->table_name 
                WHERE Tit=? AND CodAsig=?
                ORDER BY NombreAsig;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute(array($this->titulacion,$this->id));
        return $stmt->fetchAll(PDO::FETCH_NUM);
    }

}

?>