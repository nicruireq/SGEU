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

    public function createCategoria() {
        $ssql = "INSERT INTO $this->table_name(NombreCat, IsDeleted) 
                    VALUES (?,0);";
        $stmt = $this->conn->prepare($ssql);
        return $stmt->execute(array($this->nombre));
    }

    public function deleteCategoriaById() {
        // borrar todas las preguntas y respuestas de la categoria
        // todas las subcategorias de la categoria
        // y finalmente la categoria
        $ssql = "UPDATE $this->table_name
                    SET IsDeleted=1
                WHERE IdCat=:cat;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->bindValue("cat", $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateCategoriaById()
    {
        $ssql = "UPDATE $this->table_name 
                    SET NombreCat=? 
                WHERE IdCat=?;";
        $stmt = $this->conn->prepare($ssql);
        return $stmt->execute(array($this->nombre, $this->id));
    }

}

?>