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

}

?>