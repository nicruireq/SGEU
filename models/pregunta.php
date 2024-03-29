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

    public function getPreguntaEnunciadoById() {
        $ssql = "SELECT Enunciado
                        FROM $this->table_name
                    WHERE IdPreg=?
                    ORDER BY Enunciado;";
            $stmt = $this->conn->prepare($ssql);
            $stmt->execute(array($this->id));
            return $stmt->fetch(PDO::FETCH_NUM);
    }
    
    public function getPreguntaByEncCatSub()    
    {
        if ($this->subcategoria) {
            $ssql = "SELECT IdPreg, Enunciado, RelacionProfesor 
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
            $ssql = "SELECT IdPreg, Enunciado, RelacionProfesor 
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

    public function getPreguntaBycat() {
        $ssql = "SELECT IdPreg, Enunciado, RelacionProfesor FROM $this->table_name
                    WHERE Categoria=? AND Subcategoria IS NULL AND IsDeleted=0;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute(array($this->categoria));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPreguntaBySubcat() {
        $ssql = "SELECT IdPreg, Enunciado, RelacionProfesor FROM $this->table_name
                    WHERE Subcategoria=? AND IsDeleted=0;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->execute(array($this->subcategoria));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        return $this->conn->lastInsertId();
    }

    public function deletePreguntaById() {
        $ssql = "UPDATE $this->table_name
                    SET IsDeleted=1
                WHERE IdPreg=?;";
        $stmt = $this->conn->prepare($ssql);
        return $stmt->execute(array(
            $this->id
        ));
    }

    public function deletePreguntaByCat() {
        $ssql = "UPDATE $this->table_name
                    SET IsDeleted=1
                WHERE Categoria=:cat;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->bindValue("cat", $this->categoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deletePreguntaBySubcat() {
        $ssql = "UPDATE $this->table_name
                    SET IsDeleted=1
                WHERE Subcategoria=:sub;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->bindValue("sub", $this->subcategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

}

?>