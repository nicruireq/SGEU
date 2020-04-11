<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();

// si no es un usuario logeado sale...
if (empty($_SESSION['username'])) {
    session_destroy();
    header("Location: index.php", true, 301);
    exit();
}

require_once('models/encuesta.php');
// objeto de conexion a bd
$database = new Database();
// para consultas de encuesta
$encMod = new Encuesta($database);
$encuestas = $encMod->getEncuestaAll();

// comprobar datos de formularios

// para indicar si se produce un error
$error = false;
/*  Inicializar mensajes de error, 
    si el error no se ha producido el mensaje es "" 
*/
$errores = array(
    'formTitEncuesta' => "",
    'formInsEncuesta' => ""
);

require_once('utilities/formUtils.php');

// para el formulario de seleccion de encuesta
$selEncuesta = $_REQUEST['selEncuesta'];
$subEncuesta = $_REQUEST['subEncuesta'];

if (isset($subEncuesta)) {
    if (isset($selEncuesta) && $selEncuesta != "") {
        $sid = $encuestas[$selEncuesta]['IdEnc'];
        $enc = $encMod->getEncuestaById($sid);
        $_SESSION['idEnc'] = $sid;
        $_SESSION['descripcion'] = $enc['Descripcion'];
        $_SESSION['instrucciones'] = $enc['Instrucciones'];
        // para mostrar el resto del formulario
        $_SESSION['formDatosEncuesta'] = true;
        
    }
}

// formulario edicion encuesta
$subEditEnc = $_REQUEST['subEditEnc'];
$encTitTxt = trim(strip_tags($_REQUEST['encTitTxt']));
$encInsTxt = trim(strip_tags($_REQUEST['encInsTxt']));

if (isset($subEditEnc)) {
    // validar datos y fijar errores
    if ($encTitTxt == ""
            || strlen($encTitTxt) > 120) {
        $error = true;
        $errores['formTitEncuesta'] = 
            "El título incluye caracteres prohibidos o es demasiado largo";
    }

    if ($encInsTxt == ""
            || strlen($encInsTxt) > 530) {
        $error = true;
        $errores['formInsEncuesta'] = 
            "Las instrucciones incluyen caracteres prohibidos o es demasiado largo";
    }
    // si no hay errores en los formularios
    if ($errores['formTitEncuesta'] == "" 
        && $errores['formInsEncuesta'] == "") {
            $encMod->id = $_SESSION['idEnc'];
            $encMod->titulo = $encTitTxt;
            $encMod->instrucciones = $encInsTxt;
            $encMod->updateEncuesta();
            $_SESSION['descripcion'] = $encMod->titulo;
            $_SESSION['instrucciones'] = $encMod->instrucciones;
    }
}



?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Gestion</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="container p-3 my-3 border">
        <h1 class="text-center">Gestionar encuestas</h1>
        <div class="row p-3 my-3">
            <h3>Selección de encuesta</h3>
        </div>
        <div class="row p-3 my-1">
            <form action="gestion.php" method="POST" class="form-inline">
                <div class="form-group">
                    <label for="sEnc">Seleccione una encuesta:</label>
                    <select name="selEncuesta" id="sEnc" class="form-control">
                        <option value="">Seleccione una encuesta...</option>
                        <?php
                        for ($i = 0; $i < count($encuestas); $i++) {
                            echo '<option value="' . $i . '">' .
                                $encuestas[$i]['Descripcion'] .
                                '</option>';
                        }
                        ?>
                    </select>
                    <input type="submit" name="subEncuesta" value="Editar" class="btn btn-primary">
                </div>
            </form>
        </div>
        <?php
        if ($_SESSION['formDatosEncuesta']) {
            require_once('utilities/formDatosEncuesta.php');
        }
        ?>
        <a href="logout.php">SALIR</a>
        <!--
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="user">Usuario: </label>
            <input type="email" name="user" class="form-control">
        </div>
        <div class="form-group">
            <label for="pass">Contraseña: </label>
            <input type="password" name="pass" class="form-control">
        </div>
        <input type="submit" name="autenticar" value="Entrar" class="btn btn-primary">
    </form>
    -->
    </div>

</body>

</html>