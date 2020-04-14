<?php

error_reporting(E_ALL & ~E_NOTICE);
session_start();

// si no es un usuario logeado sale...
if (empty($_SESSION['username'])) {
    session_destroy();
    header("Location: index.php", true, 301);
    exit();
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
// objeto de conexion a bd
$database = new Database();
// para consultas de encuesta
$encMod = new Encuesta($database);
$encuestas = $encMod->getEncuestaAll();
$encuesta = $encuestas[0];
$_SESSION['encuestaId'] = $encuesta['IdEnc'];
// titulaciones
$titMod = new Titulacion($database);
$titulaciones = $titMod->getTitulacionAll();
// asignaturas
$asigMod = new Asignatura($database);
// profesores
$profMod = new Profesor($database);

$error = false;
$errores = array(
    'formSelTit' => "",
    'formSelAsig' => "",
    'formSelGrp' => "",
    'formSelProf' => "",
    'formEncuesta' => ""
);

// recuperar asignaturas
$subTitulacion = $_REQUEST['subTitulacion'];
$selTit = $_REQUEST['selTit'];
if (isset($subTitulacion)) {
    if ($selTit == "" || !ctype_digit($selTit)) {
        $error = true;
        $errores['formSelTit'] = "Tiene que seleccionar una titulacion";
    } else {
        $_SESSION['titulacionId'] = $titulaciones[$selTit]['CodTit'];
        $_SESSION['titulacionNombre'] = $titulaciones[$selTit]['NombreTit'];
        $asigMod->titulacion = $titulaciones[$selTit]['CodTit'];
        $asignaturas = $asigMod->getAsignaturaByTit();
        // como cambian las asignaturas al cambiar
        // de titulacion hay que borrar la asignatura,grupos y profesores marcada actual
        if (isset($_SESSION['asignaturaId'])
            && isset($_SESSION['asignaturaNombre'])
            && isset($_SESSION['asignaturaNumGrupos'])) {
                unset($_SESSION['asignaturaId']);
                unset($_SESSION['asignaturaNombre']);
                unset($_SESSION['asignaturaNumGrupos']);
        }
        if (isset($_SESSION['grupo'])) {
            unset($_SESSION['grupo']);
        }
        if (isset($_SESSION['profesorId']) &&
            isset($_SESSION['profesorNombre']) && 
            isset($_SESSION['profesorApellidos'])) {
                unset($_SESSION['profesorId']);
                unset($_SESSION['profesorNombre']);
                unset($_SESSION['profesorApellidos']);
        }
    }
}
// para que se muestren siempre las asignaturas
if (isset($_SESSION['titulacionId'])) {
    $asigMod->titulacion = $_SESSION['titulacionId'];
    $asignaturas = $asigMod->getAsignaturaByTit();
}

// seleccion de asignatura
$subAsig = $_REQUEST['subAsig'];
$selAsig = $_REQUEST['selAsig'];
if (isset($subAsig)) {
    if ($selAsig == "" || !ctype_digit($selAsig)) {
        $error = true;
        $errores['formSelAsig'] = "Tiene que seleccionar una asignatura";
    } else {
        $asigMod->titulacion = $_SESSION['titulacionId'];
        $asignaturas = $asigMod->getAsignaturaByTit();
        $_SESSION['asignaturaId'] = $asignaturas[$selAsig]['CodAsig'];
        $_SESSION['asignaturaNombre'] = $asignaturas[$selAsig]['NombreAsig'];
        $_SESSION['asignaturaNumGrupos'] = $asignaturas[$selAsig]['NumGrupos'];
        // al cambiar de asignatura cambian grupos y profesores
        if (isset($_SESSION['grupo'])) {
            unset($_SESSION['grupo']);
        }
        if (isset($_SESSION['profesorId']) &&
            isset($_SESSION['profesorNombre']) && 
            isset($_SESSION['profesorApellidos'])) {
                unset($_SESSION['profesorId']);
                unset($_SESSION['profesorNombre']);
                unset($_SESSION['profesorApellidos']);
        }
    }
}
// eleccion de grupo
$subGrp = $_REQUEST['subGrp'];
$selGrp = $_REQUEST['selGrp'];
if (isset($subGrp)) {
    if ($selGrp == "" || !ctype_digit($selGrp)) {
        $error = true;
        $errores['formSelGrp'] = "Tiene que seleccionar un grupo";
    } else {
        $_SESSION['grupo'] = $selGrp;
    }
}

// eleccion de profesor a evaluar
$profesores = $profMod->getProfesorByAsig(
    $_SESSION['titulacionId'], 
    $_SESSION['asignaturaId']
);
$subProf = $_REQUEST['subProf'];
$selProf = $_REQUEST['selProf'];
if (isset($subProf)) {
    if ($selProf == "" || !ctype_digit($selProf)) {
        $error = true;
        $errores['formSelProf'] = "Tiene que seleccionar un profesor para evaluar";
    } else {
        $_SESSION['profesorId'] = $profesores[$selProf]['CodProf'];
        $_SESSION['profesorNombre'] = $profesores[$selProf]['NombreProf'];
        $_SESSION['profesorApellidos'] = $profesores[$selProf]['ApellidosProf'];
    }
}

// comprobar si se han rellenado los primeros datos basicos
function isCodasigSelected() {
    return  isset($_SESSION['titulacionId']) &&
            isset($_SESSION['titulacionNombre']) &&
            isset($_SESSION['asignaturaId']) &&
            isset($_SESSION['asignaturaNombre']) &&
            isset($_SESSION['asignaturaNumGrupos']) &&
            isset($_SESSION['grupo']) &&
            isset($_SESSION['profesorId']) &&
            isset($_SESSION['profesorNombre']) && 
            isset($_SESSION['profesorApellidos']);

}

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

    <div class="container p-3 my-3 border">
        <div class="row p-3 my-3">
            <h3>Seleccione asignatura y profesorado para ver sus estadísticas</h3>
        </div>
        <!-- seleccion titulacion -->
        <div class="row p-3 my-1">
            <form action="estadisticas.php" method="POST" class="form-inline">
                <div class="form-group">
                    <label for="selTit">Seleccione una titulación:</label>
                    <select name="selTit" id="selTit" class="form-control">
                        <option value="">Seleccione una...</option>
                        <?php
                        for ($i = 0; $i < count($titulaciones); $i++) {
                            echo '<option value="' . $i . '">' .
                                $titulaciones[$i]['NombreTit'] .
                                '</option>';
                        }
                        ?>
                    </select>
                    <input type="submit" name="subTitulacion" value="Seleccionar" class="btn-sm btn-primary">
                </div>
            </form>
        </div>
        <div class="row p-3 my-1">
            <?php
                if ($error && $errores['formSelTit'] != "") {
                    msg_alert('formSelTit');
                }
            ?>
        </div>
        <?php if (!empty($_SESSION['titulacionId'])) : ?>
            <div class="alert alert-info">
                Titulación seleccionada: <?php echo $_SESSION['titulacionNombre']; ?> 
            </div>
        <?php endif; ?>
        <!-- seleccion asignatura -->
        <div class="row p-3 my-1">
            <form action="estadisticas.php" method="POST" class="form-inline">
                <div class="form-group">
                    <label for="selAsig">Seleccione una asignatura:</label>
                    <select name="selAsig" id="selAsig" class="form-control">
                        <option value="">Seleccione una...</option>
                        <?php
                        for ($i = 0; $i < count($asignaturas); $i++) {
                            echo '<option value="' . $i . '">' .
                                $asignaturas[$i]['NombreAsig'] .
                                '</option>';
                        }
                        ?>
                    </select>
                    <input type="submit" name="subAsig" value="Seleccionar" class="btn-sm btn-primary">
                </div>
            </form>
        </div>
        <div class="row p-3 my-1">
            <?php
                if ($error && $errores['formSelAsig'] != "") {
                    msg_alert('formSelAsig');
                }
            ?>
        </div>
        <?php if (!empty($_SESSION['asignaturaId'])) : ?>
            <div class="alert alert-info">
                Asignatura seleccionada: <?php echo $_SESSION['asignaturaNombre']; ?> 
            </div>
        <?php endif; ?>
        <!-- seleccion grupo -->
        <div class="row p-3 my-1">
            <form action="estadisticas.php" method="POST" class="form-inline">
                <div class="form-group">
                    <label for="selGrp">Seleccione un grupo:</label>
                    <select name="selGrp" id="selGrp" class="form-control">
                        <option value="">Seleccione uno...</option>
                        <?php
                            for ($i = 0; $i < $_SESSION['asignaturaNumGrupos']; $i++) {
                                echo '<option value="' . ($i+1) . '">'.($i+1).'</option>';
                            }
                        ?>
                    </select>
                    <input type="submit" name="subGrp" value="Seleccionar" class="btn-sm btn-primary">
                </div>
            </form>
        </div>
        <div class="row p-3 my-1">
            <?php
                if ($error && $errores['formSelGrp'] != "") {
                    msg_alert('formSelGrp');
                }
            ?>
        </div>
        <?php if (!empty($_SESSION['grupo'])) : ?>
            <div class="alert alert-info">
                Grupo seleccionado: <?php echo $_SESSION['grupo']; ?> 
            </div>
        <?php endif; ?>
        <!-- Seleccion del profesor a evaluar -->
        <div class="row p-3 my-1">
            <form action="estadisticas.php" method="POST" class="form-inline">
                <div class="form-group">
                    <label for="selProf">Seleccione un profesor a evaluar:</label>
                    <select name="selProf" id="selProf" class="form-control">
                        <option value="">Seleccione uno...</option>
                        <?php
                            for ($i = 0; $i < count($profesores); $i++) {
                                echo '<option value="' . $i . '">'.
                                    $profesores[$i]['ApellidosProf'].', '.
                                    $profesores[$i]['NombreProf']
                                .'</option>';
                            }
                        ?>
                    </select>
                    <input type="submit" name="subProf" value="Seleccionar" class="btn-sm btn-primary">
                </div>
            </form>
        </div>
        <div class="row p-3 my-1">
            <?php
                if ($error && $errores['formSelProf'] != "") {
                    msg_alert('formSelProf');
                }
            ?>
        </div>
        <?php if (!empty($_SESSION['profesorId'])) : ?>
            <div class="alert alert-info">
                Profesor seleccionado: <?php echo $_SESSION['profesorApellidos'].', '.$_SESSION['profesorNombre']; ?>  
            </div>
        <?php endif; ?>
        <div class="row p-3 my-1">
        <form action="estadisticaProcesador.php" method="POST">
            <div class="form-group">
              <input type="submit" name="subEstadisticas" value="Ver estadísticas" class="btn-sm btn-primary">
            </div>
        </form>
        </div>
        
        <a href="logout.php">SALIR</a>
    </div>
</body>
</html>