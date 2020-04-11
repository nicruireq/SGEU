<?php
    error_reporting(E_ALL & ~E_NOTICE);
    // fichero configuracion base de datos
    require('config/db.php');
    // datos de formulario
    $autenticar = $_REQUEST['autenticar'];
    $usuario = $_REQUEST['user'];
    $pass = $_REQUEST['pass'];
    // para indicar si se produce un error
    $error = false;
    /*  Inicializar mensajes de error, 
        si el error no se ha producido el mensaje es "" 
    */
    $errores = array(
        'form_user' => "",
        'form_pass' => "",
        'db' => "",
        'db_user' => "",
        'db_pass' => ""
    );
    
    require_once('utilities/formUtils.php');

    if (isset($autenticar)) {
        // comprobar formato formularios
        if (trim($usuario) == "" || !ctype_alnum($usuario)) {
            $error = true;
            $errores['form_user'] = "Introduzca un usuario válido.";
        }

        if (trim($pass) == "" || !ctype_alnum($pass)) {
            $error = true;
            $errores['form_pass'] = "Introduzca una contraseña válida.";
        }
    }
    // comprobar usuario con la bd
    if (!$error && isset($autenticar)) {
        try {
            $base = new PDO($dbconn['dsn'], $dbconn['user'], $dbconn['pass']);
            $base->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $sql = 'SELECT Username, Pass, Estado FROM USUARIOS WHERE Username=:user;';
            $resultado = $base->prepare($sql);
            $resultado->execute(array(':user'=>$usuario));
            // usuario existe
            if ($resultado->rowCount() == 1) {
                $row = $resultado->fetch(PDO::FETCH_ASSOC);
                if (strcmp($row['Pass'],$pass) == 0) {
                    // Crear sesion
                    $sql = "UPDATE USUARIOS SET Estado = 1 WHERE Username ='" . $row['Username'] . "';";
                    $base->exec($sql);
                    session_start();
                    $_SESSION['username'] = $row['Username'];
                    // redireccion permanente
                    unset($errores);
                    header("Location: gestion.php", true, 301);
                    exit();
                } else {
                    // pass incorrecta
                    $error = true;
                    $errores['db_pass'] = "Contraseña incorrecta.";
                }
            } else {    
                // usuario inexistente
                $error = true;
                $errores['db_user'] = "El usuario no existe.";
            }

        } catch (PDOException $th) {
            $error = true;
            $errores['db'] = "No se puede concectar en estos momentos.";
        } finally {
            $base = null;
        }
        
    }

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container p-3 my-3 border">
    <h1 class="text-center">Login</h1>
    <?php
        if ($error && $errores['db']) {
            msg_alert('db');
        }
    ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="user">Usuario: </label>
            <input type="text" name="user" class="form-control">
            <?php
                if ($error && $errores['form_user'] != "") {
                    msg_alert('form_user');
                } else if ($error && $errores['db_user'] != "") {
                    msg_alert('db_user');
                }
            ?>
        </div>
        <div class="form-group">
            <label for="pass">Contraseña: </label>
            <input type="password" name="pass" class="form-control">
            <?php
                if ($error && $errores['form_pass'] != "") {
                    msg_alert('form_pass');
                } else if ($error && $errores['db_pass'] != "") {
                    msg_alert('db_pass');
                }
            ?>
        </div>
        <input type="submit" name="autenticar" value="Entrar" class="btn btn-primary">
    </form>
</div>

</body>
</html>
