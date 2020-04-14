<?php

error_reporting(E_ALL & ~E_NOTICE);
session_start();

// si no es un usuario logeado sale...
if (empty($_SESSION['username'])) {
    session_destroy();
    header("Location: index.php", true, 301);
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>SGEU</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
  
<div class="container p-3 my-3 border">
  <h1 class="text-center">Sistema de Gestión de Encuestas</h1>
  <h2 class="text-center">Zona privada</h1>
    <a href="gestion.php" class="btn btn-primary btn-block" role="button">Gestionar encuesta</a>
    <a href="estadisticas.php" class="btn btn-primary btn-block" role="button">Estadísticas</a>
    <a href="logout.php" class="btn btn-primary btn-block" role="button">Cerrar sesión</a>
</div>

</body>
</html>
