<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();

$error = false;
$errores = array(
    'postInput' => "",
    'sessionEmpty' => ""
);

// descartar valor del boton de submit
unset($_POST['subRespuestas']);
// comprobar entrada
foreach ($_POST as $pregunta => $respuesta) {
    if (empty($respuesta)) {
        $error = true;
        $errores['postInput'] = "Se ha producido un error 
                                al procesar su petición, 
                                algunas respuestas no son válidas";
    }
}

if  (!isset($_SESSION['encuestaId']) ||
    !isset($_SESSION['titulacionId']) ||
    !isset($_SESSION['asignaturaId']) ||
    !isset($_SESSION['grupo']) ||
    !isset($_SESSION['profesorId'])) {
        $error = true;
        $errores['sessionEmpty'] = "No se ha podido procesar su solicitud, faltan datos";
}

// procesar para introducir en bd
require_once('config/database.php');
require_once('models/encuesta_resp.php');
require_once('models/respuestas.php');

$database = new Database();
$encuestarespMod = new Encuesta_resp($database);
$respMod = new Respuestas($database);

if (!$error && $errores['postInput'] == ""
        && $errores['sessionEmpty'] == "") {
    // crear nueva instancia de una encuesta respondida
    $encuestarespMod->titulacion = $_SESSION['titulacionId'];
    $encuestarespMod->asignatura = $_SESSION['asignaturaId'];
    $encuestarespMod->encuesta = $_SESSION['encuestaId'];
    $encuestarespMod->grupo = $_SESSION['grupo'];
    $encuestarespMod->profesor = $_SESSION['profesorId'];
    $idIns = $encuestarespMod->createEncuestaResp();
    // crear respuestas de la encuesta respondida
    $respMod->encresp = $idIns;
    foreach ($_POST as $preg => $resp) {
        $respMod->pregunta = $preg;
        $respMod->opcion = $resp;
        $respMod->createRespuesta();
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Encuesta procesada</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="container p-3 my-3 border"> 
    <?php if ($error): ?>
            <p class=".bg-danger">
            <h1>
            <?php
                if ($errores['postInput'] != "")
                    echo $errores['postInput'];
                if ($errores['sessionEmpty'] != "")
                    echo $errores['sessionEmpty'];
            ?>
            </h1>
            </p>
    <?php else: ?>
        <h1>Sus respuestas han sido guardadas. Gracias por participar.</h1>
    <?php endif; ?>
    </div>

</body>