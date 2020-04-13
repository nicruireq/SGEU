<?php

require_once('model.php');

class Opcion extends Model {

    private $table_name = "OPCION";
    // propiedades
    public $id;
    public $texto;
    public $pregunta;
    
    /**
     * introduce todas las opciones del array $ops
     * en la tabla
     */
    public function createOpcionesPregunta($ops)
    {
        $ssql = "INSERT INTO $this->table_name(Texto,Pregunta,IsDeleted) 
                    VALUES (?,$this->pregunta,0);";
        $stmt = $this->conn->prepare($ssql);
        for ($i=0; $i < count($ops); $i++) { 
            $this->texto = $ops[$i];
            $last = $stmt->execute(array(
                $this->texto
            ));
        }
        return $last;
    }

    public function deleteOpcionesByPregunta() {
        $ssql = "UPDATE $this->table_name
                    SET IsDeleted=1
                WHERE Pregunta=:idpreg;";
        $stmt = $this->conn->prepare($ssql);
        $stmt->bindValue("idpreg", $this->pregunta, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteOpcionesByPreguntas($pregs) {
        
    }

}

?>