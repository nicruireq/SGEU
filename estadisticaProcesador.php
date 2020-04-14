<?php

error_reporting(E_ALL & ~E_NOTICE);
session_start();

// si no es un usuario logeado sale...
if (empty($_SESSION['username'])) {
    session_destroy();
    header("Location: index.php", true, 301);
    exit();
}

$error = false;
$errores = array(
    'sessionEmpty' => ""
);

if (
    !isset($_SESSION['encuestaId']) ||
    !isset($_SESSION['titulacionId']) ||
    !isset($_SESSION['asignaturaId']) ||
    !isset($_SESSION['grupo']) ||
    !isset($_SESSION['profesorId'])
) {
    $error = true;
    $errores['sessionEmpty'] = "No se ha podido procesar su solicitud, faltan datos";
}

require_once('utilities/formUtils.php');
require_once('models/encuesta.php');
require_once('models/categoria.php');
require_once('models/subcategoria.php');
require_once('models/pregunta.php');
require_once('models/opcion.php');
require_once('models/titulacion.php');
require_once('models/profesor.php');
require_once('models/asignatura.php');
require_once('models/respuestas.php');
// objeto de conexion a bd
$database = new Database();
$asigMod = new Asignatura($database);
$asigMod->titulacion = $_SESSION['titulacionId'];
$asigMod->id = $_SESSION['asignaturaId'];
$catMod = new Categoria($database);
$subcatMod = new Subcategoria($database);
$pregMod = new Pregunta($database);
$opsMod = new Opcion($database);
$respMod = new Respuestas($database);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Estadísticas</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="container-fluid p-3 my-3 border">
        <?php if ($error) : ?>
            <p class=".bg-danger">
                <h1><?php if ($errores['sessionEmpty'] != "") echo $errores['sessionEmpty']; ?></h1>
            </p>
        <?php else : ?>
            <div class="card">
                <div class="card-header">Mostrando estadísticas para:</div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">Profesor/a:<?php echo $_SESSION['profesorApellidos'] . ', ' . $_SESSION['profesorNombre']; ?></li>
                        <li class="list-group-item">Asignatura:<?php echo $_SESSION['asignaturaNombre']; ?></li>
                        <li class="list-group-item">Titulación:<?php echo $_SESSION['titulacionNombre']; ?></li>
                    </ul>
                </div>
            </div>
            <!-- cuenta, avg y std de asignatura-->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Pregunta</th>
                        <th>Nº Respuestas</th>
                        <th>Media</th>
                        <th>Desviación típica</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $avgGlobal = $stdGlobal = 0;
                    $filas = $respMod->getCountAvgStdAllEncuestasFromAsig($_SESSION['asignaturaId']);
                    for ($i = 0; $i < count($filas); $i++) {
                        $pregMod->id = $filas[$i][0];
                        $nomPreg = $pregMod->getPreguntaEnunciadoById();
                        echo '<tr>';
                        echo '<td>'.$nomPreg[0].'</td>';
                        echo '<td>'.$filas[$i][1].'</td>';
                        echo '<td>'.$filas[$i][2].'</td>';
                        echo '<td>'.$filas[$i][3].'</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="logout.php">SALIR</a>
    </div>

</body>