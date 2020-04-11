<?php

require_once('model.php');

class Categoria extends Model {

    private $table_name = "CATEGORIA";
    // propiedades
    public $id;
    public $nombre;

    public function getCategoriaAll() {
        $ssql = "SELECT IdCat, NombreCat 
                    FROM $this->table_name 
                WHERE IsDeleted=0 
                ORDER BY NombreCat;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>