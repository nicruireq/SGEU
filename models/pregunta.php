<?php

require_once('model.php');

class Pregunta extends Model {

    private $table_name = "PREGUNTA";
    // propiedades
    public $id;
    public $enunciado;
    public $encuesta;
    public $relprof;
    public $subcategoria;
    public $categoria;
    
    public function getPreguntaByEncCatSub()    
    {
        if ($this->subcategoria) {
            $ssql = "SELECT IdPreg, Enunciado 
                        FROM $this->table_name
                    WHERE Encuesta=? AND Subcategoria=? AND Categoria=? AND IsDeleted=0
                    ORDER BY Enunciado;";
            $stmt = $this->conn->prepare($ssql);
            $stmt->execute(array(
                $this->encuesta,
                $this->subcategoria,
                $this->categoria
            ));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $ssql = "SELECT IdPreg, Enunciado 
                    FROM $this->table_name
                WHERE Encuesta=? AND Categoria=? AND IsDeleted=0
                ORDER BY Enunciado;";
            $stmt = $this->conn->prepare($ssql);
            $stmt->execute(array(
                $this->encuesta,
                $this->categoria
            ));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

}

?>