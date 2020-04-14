<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();

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
    <title>Rellenar encuesta</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="container p-3 my-3 border">
        <h1 class="text-center"><?php echo $encuesta['Descripcion']; ?></h1>
        <div class="row p-3 my-3">
            <h3>Instrucciones</h3>
        </div>
        <div class="row p-3 my-3">
            <?php echo $encuesta['Instrucciones']; ?>
        </div>
        <div class="row p-3 my-3">
            <h3>Código asignatura</h3>
        </div>
        <!-- seleccion titulacion -->
        <div class="row p-3 my-1">
            <form action="encuesta.php" method="POST" class="form-inline">
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
            <form action="encuesta.php" method="POST" class="form-inline">
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
            <form action="encuesta.php" method="POST" class="form-inline">
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
            <form action="encuesta.php" method="POST" class="form-inline">
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
        <!-- Imprimir formulario con todas las preguntas -->                
        <?php
            $catMod = new Categoria($database);
            $subcatMod = new Subcategoria($database);
            $pregMod = new Pregunta($database);
            $pregMod->encuesta = $encuesta['IdEnc'];
            $opsMod = new Opcion($database);
            $categorias = array();
            $subcategorias = array();
            $preguntas = array();
            $opciones = array();
            // recuperar profesores de la asignatura, categorias
            if (isCodasigSelected()) {
                $categorias = $catMod->getCategoriaAll();
                
                echo '<div class="row p-3 my-1">';
                echo '<form action="encuestaProcesador.php" method="POST" class="form-inline">';
                for ($i=0; $i<count($categorias); ++$i) {
                    echo '<h3>'.$categorias[$i]['NombreCat'].'</h3>';
                    // primero mostrar preguntas de la categoria que no tienen subcategoria
                    $pregMod->categoria = $categorias[$i]['IdCat'];
                    $preguntas = $pregMod->getPreguntaBycat();
                    for ($j=0; $j<count($preguntas);++$j) {
                        // recuperar opciones
                        echo '<div class="form-group">';
                        echo '<label for="sel'.$i.$j.'">'.$preguntas[$j]['Enunciado'].'</label>';
                        $opsMod->pregunta = $preguntas[$j]['IdPreg'];
                        $opciones = $opsMod->getOpcionByPreg();
                        echo '<select name="'.$preguntas[$j]['IdPreg'].'" id="sel'.$i.$j.'" class="form-control" required>';
                        echo '<option value="">Seleccione uno...</option>';
                        for ($k=0; $k<count($opciones); ++$k) {
                            echo '<option value="'.$opciones[$k]['IdOp'].'">'.$opciones[$k]['Texto'].'</option>';
                        }
                        echo '</select>';
                        echo '</div>';
                    }
                    // imprimir preguntas que tambien estan asociadas a una subcategoria
                    $subcatMod->categoria = $categorias[$i]['IdCat'];
                    $subcategorias = $subcatMod->getSubcategoriaByCat();
                    for ($j=0; $j<count($subcategorias); ++$j) {
                        echo '<h4>'.$subcategorias[$j]['NombreSub'].'</h4>';
                        $pregMod->subcategoria = $subcategorias[$j]['IdSub'];
                        $preguntas = $pregMod->getPreguntaByEncCatSub();
                        for ($k=0; $k<count($preguntas);++$k) {
                            // recuperar opciones
                            echo '<div class="form-group">';
                            echo '<label for="sel'.$i.$j.$k.'">'.$preguntas[$k]['Enunciado'].'</label>';
                            $opsMod->pregunta = $preguntas[$k]['IdPreg'];
                            $opciones = $opsMod->getOpcionByPreg();
                            echo '<select name="'.$preguntas[$k]['IdPreg'].'" id="sel'.$i.$j.$k.'" class="form-control" required>';
                            echo '<option value="">Seleccione uno...</option>';
                            for ($l=0; $l<count($opciones); ++$l) {
                                echo '<option value="'.$opciones[$l]['IdOp'].'">'.$opciones[$l]['Texto'].'</option>';
                            }
                            echo '</select>';
                            echo '</div>';
                        }
                    }
                }
                echo '<input type="submit" name="subRespuestas" value="Enviar" class="btn-sm btn-primary">';
                echo '</form>';
                echo '</div>';
            }
        ?>
        
        <!-- fin -->
    </div>

</body>

</html>