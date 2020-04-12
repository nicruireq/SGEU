<?php

require_once('model.php');

class Subcategoria extends Model
{

    private $table_name = "SUBCATEGORIA";
    // propiedades
    public $id;
    public $nombre;
    public $categoria;

    public function getSubcategoriaByCat()
    {
        $ssql = "SELECT IdSub, NombreSub 
                    FROM $this->table_name 
                WHERE Categoria=? AND IsDeleted=0 
                ORDER BY NombreSub;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute(array($this->categoria));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createSubcategoria()
    {
        $ssql = "INSERT INTO $this->table_name(NombreSub, Categoria, IsDeleted) 
                    VALUES (?,?,0);";
        $stmt = $this->conn->prepare($ssql);
        return $stmt->execute(array($this->nombre, $this->categoria));
    }

    public function updateSubcategoriaById()
    {
        $ssql = "UPDATE $this->table_name 
                    SET NombreSub=? 
                WHERE IdSub=?;";
        $stmt = $this->conn->prepare($ssql);
        return $stmt->execute(array($this->nombre, $this->id));
    }

    public function deleteSubcategoriaById() {
        // borrar todas las preguntas y respuestas de la categoria
        // todas las subcategorias de la categoria
        // y finalmente la categoria

    }
}
