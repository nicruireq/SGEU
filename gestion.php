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
require_once('models/categoria.php');
require_once('models/subcategoria.php');
require_once('models/pregunta.php');
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
    'formInsEncuesta' => "",
    'formNomCategoria' => "",
    'formEditCategoria' => "",
    'formNomSubcategoria' => "",
    'formApplyCatNom' => "",
    'formNomSubcategoria' => "",
    'formEditSubcategoria' => "",
    'formEditPreg' => ""
);

require_once('utilities/formUtils.php');

/**************************
 * formulario ENCUESTAS
 **************************/
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

// formulario EDICION ENCUESTA
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

/****************************
 * formulario CATEGORIAS
 ****************************/
$catMod = new Categoria($database);
$categorias = $catMod->getCategoriaAll();

// crear nueva categoria
$subNewCat = $_REQUEST['subNewCat'];    // boton nueva categoria
$catNomTxt = trim(strip_tags($_REQUEST['catNomTxt']));    // campo de texto nombre categoria
if (isset($subNewCat)) {
    if ($catNomTxt == "" 
        || strlen($catNomTxt) > 100) {
            $error = true;
            $errores['formNomCategoria'] = "Nombre de categoría no válido";
    } else {
    //if ($errores['formNomCategoria'] == "") {
        $catMod->nombre = $catNomTxt;
        $catMod->createCategoria();
        // hay que actualizar las categorias en la vista
        $categorias = $catMod->getCategoriaAll();
    }
}

// editar categoria
$subEditCat = $_REQUEST['subEditCat'];
$catSel = $_REQUEST['catSel'];
if (isset($subEditCat)) {
    if ($catSel == "" || !ctype_digit($catSel)) {
        $error = true;
        $errores['formEditCategoria'] = "Seleccione una de las categorías";
    } else {
        /* permite mostrar el formulario para 
           editar el nombre de la categoria */
        $_SESSION['showEditboxCategoria'] = true;
        $_SESSION['idCategoriaEditar'] = $categorias[$catSel]['IdCat'];
    }
}
//  formulario adicional para editar nombre de categoria
if ($_SESSION['showEditboxCategoria']) {
    $subApplyCat = $_REQUEST['subApplyCat'];
    $catEditNom = trim(strip_tags($_REQUEST['catEditNom']));
    if (isset($subApplyCat)) {
        if ($catEditNom == "" 
            || strlen($catEditNom) > 100) {
                $error = true;
                $errores['formApplyCatNom'] = "Nombre de categoría no válido";
        } else {
            //update
            $catMod->id = $_SESSION['idCategoriaEditar'];
            $catMod->nombre = $catEditNom;
            $catMod->updateCategoriaById();
            // actualizar las categorias mostradas en el desplegable
            $categorias = $catMod->getCategoriaAll();
            // clean
            $_SESSION['showEditboxCategoria'] = false;
            unset($_SESSION['idCategoriaEditar']);  // PROBLEMAS?
        }
    }

}

// eliminar categoria
$subDelCat = $_REQUEST['subDelCat'];
if (isset($subDelCat)) {
    if ($catSel == "" || !ctype_digit($catSel)) {
        $error = true;
        $errores['formEditCategoria'] = "Seleccione una de las categorías";
    } else {
        $actual = $categorias[$catSel];
        $catMod->id = $actual['IdCat'];
        $catMod->deleteCategoriaById();
        // update categorias para vista
    }
}

// marcar categoria como seleccionada
$subSelCat = $_REQUEST['subSelCat'];
if (isset($subSelCat)) {
    if ($catSel == "" || !ctype_digit($catSel)) {
        $error = true;
        $errores['formEditCategoria'] = "Seleccione una de las categorías";
    } else {
        // guardar en el session para pasar la info entre post diferentes
        $_SESSION['idCategoriaSelected'] = $categorias[$catSel]['IdCat'];
        $_SESSION['nomCategoriaSelected'] = $categorias[$catSel]['NombreCat'];
    }
}

/******************************
 * formulario SUBCATEGORIAS
 ******************************/
$subcatMod = new Subcategoria($database);
$subcatMod->categoria = $_SESSION['idCategoriaSelected'];
$subcategorias = $subcatMod->getSubcategoriaByCat();

// crear nueva subcategoria
$subNewSubcat = $_REQUEST['subNewSubcat'];    // boton nueva subcategoria
$subcatNomTxt = trim(strip_tags($_REQUEST['subcatNomTxt']));    // campo de texto nombre subcategoria
if (isset($subNewSubcat)) {
    if ($subcatNomTxt == "" 
        || strlen($subcatNomTxt) > 100) {
            $error = true;
            $errores['formNomSubcategoria'] = "Nombre de subcategoría no válido";
    } else {
        $subcatMod->nombre = $subcatNomTxt;
        $subcatMod->createSubcategoria();
        // actualizar subcategorias para la vista
        $subcategorias = $subcatMod->getSubcategoriaByCat();
    }
}

// editar subcategoria
$subEditSubcat = $_REQUEST['subEditSubcat'];
$subcatSel = $_REQUEST['subcatSel'];
if (isset($subEditSubcat)) {
    if ($subcatSel == "" || !ctype_digit($subcatSel)) {
        $error = true;
        $errores['formEditSubcategoria'] = "Seleccione una de las subcategorías";
    } else {
        // permite mostrar el formulario para 
        // editar el nombre de la subcategoria 
        $_SESSION['showEditboxSubcategoria'] = true;
        $_SESSION['idSubcategoriaEditar'] = $subcategorias[$subcatSel]['IdSub'];
        //$_SESSION['fkSubcategoriaEditar'] = $subcategorias[$subcatSel]['Categoria'];
    }
}

//  formulario adicional para editar nombre de subcategoria
if ($_SESSION['showEditboxSubcategoria']) {
    $subApplySubCat = $_REQUEST['subApplySubCat'];
    $subcatEditNom = trim(strip_tags($_REQUEST['subcatEditNom']));
    if (isset($subApplySubCat)) {
        if ($subcatEditNom == "" 
            || strlen($subcatEditNom) > 100) {
                $error = true;
                $errores['formApplySubcatNom'] = "Nombre de subcategoría no válido";
        } else {
            //update
            $subcatMod->id = $_SESSION['idSubcategoriaEditar'];
            $subcatMod->nombre = $subcatEditNom;
            //$subcatMod->categoria = $_SESSION['fkSubcategoriaEditar'];
            $subcatMod->updateSubcategoriaById();
            // actualizar las categorias mostradas en el desplegable
            $subcategorias = $subcatMod->getSubcategoriaByCat();
            // clean
            $_SESSION['showEditboxSubcategoria'] = false;
            unset($_SESSION['idCategoriaEditar']);  // PROBLEMAS?
            //unset($_SESSION['fkSubcategoriaEditar']);
        }
    }

}

// eliminar categoria
$subDelSubcat = $_REQUEST['subDelSubcat'];
if (isset($subDelSubcat)) {
    if ($subcatSel == "" || !ctype_digit($subcatSel)) {
        $error = true;
        $errores['formEditSubcategoria'] = "Seleccione una de las categorías";
    } else {
        $actual = $subcategorias[$subcatSel];
        $subcatMod->id = $actual['IdSub'];
        $subcatMod->deleteSubcategoriaById();
        // update categorias para vista
    }
}

// marcar subcategoria como seleccionada
$subSelSubcat = $_REQUEST['subSelSubcat'];
if (isset($subSelSubcat)) {
    if ($subcatSel == "" || !ctype_digit($subcatSel)) {
        $error = true;
        $errores['formEditSubcategoria'] = "Seleccione una de las categorías";
    } else {
        // guardar en el session para pasar la info entre post diferentes
        $_SESSION['idSubcategoriaSelected'] = $subcategorias[$subcatSel]['IdSub'];
        $_SESSION['nomSubcategoriaSelected'] = $subcategorias[$subcatSel]['NombreSub'];
    }
}

/***************************
 * formulario PREGUNTAS
 ***************************/
$pregMod = new Pregunta($database);
// cargar preguntas
$pregMod->encuesta = $_SESSION['idEnc'];
$pregMod->categoria = $_SESSION['idCategoriaSelected'];
if (!empty($_SESSION['idSubcategoriaSelected'])) {
    $pregMod->subcategoria = $_SESSION['idSubcategoriaSelected'];
} else {
    $pregMod->subcategoria = null;
}
$preguntas = $pregMod->getPreguntaByEncCatSub();

// edicion de pregunta
$subEditPreg = $_REQUEST['subEditPreg'];
$pregSel = $_REQUEST['pregSel'];
if (isset($subEditPreg)) {
    if ($pregSel == "" || !ctype_digit($pregSel)) {
        $error = true;
        $errores['formEditPreg'] = "Seleccione una pregunta";
    } else {
        $_SESSION['showEditFormPregunta'] = true;
        $_SESSION['showNewFormPregunta'] = false;
    }
}

// nueva pregunta
$subNewPreg = $_REQUEST['subNewPreg'];
if (isset($subNewPreg)) {
    $_SESSION['showNewFormPregunta'] = true;
    $_SESSION['showEditFormPregunta'] = false;
}

// eliminar pregunta
$subDelPreg = $_REQUEST['subDelPreg'];
if (isset($subDelPreg)) {
    if ($pregSel == "" || !ctype_digit($pregSel)) {
        $error = true;
        $errores['formEditPreg'] = "Seleccione una pregunta";
    } else {
        // codigo para eliminar
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
                    <input type="submit" name="subEncuesta" value="Editar" class="btn-sm btn-primary">
                </div>
            </form>
        </div>
        <?php
        if ($_SESSION['formDatosEncuesta']) {
            require_once('utilities/formDatosEncuesta.php');
            require_once('utilities/formDatosCategoria.php');
            require_once('utilities/formDatosSubcategoria.php');
            require_once('utilities/formDatosPregunta.php');
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