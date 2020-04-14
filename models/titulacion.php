<?php

require_once('model.php');

class Titulacion extends Model {

    private $table_name = "TITULACION";
    // propiedades
    public $id;
    public $nombre;

    public function getTitulacionAll() {
        $ssql = "SELECT CodTit, NombreTit 
                    FROM $this->table_name 
                ORDER BY NombreTit;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>