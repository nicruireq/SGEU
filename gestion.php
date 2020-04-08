<?php
    error_reporting(E_ALL & ~E_NOTICE);
    session_start();
    print_r($_SESSION);

    // si no es un usuario logeado sale...
    if (empty($_SESSION['username']) ) {
        session_destroy();
        header("Location: index.php", true, 301);
        exit();
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
    <?php
        echo $error;
        print_r($errores);
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