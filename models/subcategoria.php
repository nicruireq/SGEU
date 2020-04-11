<?php

require_once('model.php');

class Subcategoria extends Model {

    private $table_name = "SUBCATEGORIA";
    // propiedades
    public $id;
    public $nombre;

    public function getSubcategoriaAll() {
        $ssql = "SELECT IdSub, NombreSub 
                    FROM $this->table_name 
                WHERE IsDeleted=0 
                ORDER BY NombreSub;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>