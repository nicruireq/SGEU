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

    public function createPregunta()
    {
        if ($this->subcategoria) {
            $ssql = "INSERT INTO $this->table_name(Enunciado, Encuesta, RelacionProfesor, Subcategoria, Categoria, IsDeleted)
                     VALUES (:enun,:enc,:rel,:sub,:cat,0)";
            $stmt = $this->conn->prepare($ssql);
            $stmt->bindValue("rel", $this->relprof, PDO::PARAM_BOOL);
            $stmt->bindValue("enun", $this->enunciado, PDO::PARAM_STR);
            $stmt->bindValue("enc", $this->encuesta, PDO::PARAM_INT);
            $stmt->bindValue("sub", $this->subcategoria, PDO::PARAM_INT);
            $stmt->bindValue("cat", $this->categoria, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $ssql = "INSERT INTO $this->table_name(Enunciado, Encuesta, RelacionProfesor, Subcategoria, Categoria, IsDeleted)
                     VALUES (:enun,:enc,:rel,NULL,:cat,0)";
            $stmt = $this->conn->prepare($ssql);
            $stmt->bindValue("rel", $this->relprof, PDO::PARAM_BOOL);
            $stmt->bindValue("enun", $this->enunciado, PDO::PARAM_STR);
            $stmt->bindValue("enc", $this->encuesta, PDO::PARAM_INT);
            $stmt->bindValue("cat", $this->categoria, PDO::PARAM_INT);
            $stmt->execute();
        }

        return $this->lastInsertId();
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

}

?>