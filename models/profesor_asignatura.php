<?php

require_once('model.php');

class Profesor_asignatura extends Model {

    private $table_name = "PROFESOR_ASIGNATURA";
    // propiedades
    public $idProf;
    public $idTit;
    public $idAsig;

}

?>