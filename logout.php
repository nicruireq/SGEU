<?php
    error_reporting(E_ALL & ~E_NOTICE);
    session_start();
    require('config/db.php');

    try {

        if (isset($_SESSION['username'])) {
            $base = new PDO($dbconn['dsn'], $dbconn['user'], $dbconn['pass']);
            $base->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE USUARIOS SET Estado = 0 WHERE Username ='" . $_SESSION['username'] . "';";
            $base->exec($sql);
            session_destroy();
            header("Location: index.php", true, 301);
            exit();
        }

    } catch (PDOException $th) {
        die("No se puedo conectar con la bd:\n" . $th->getMessage());
    } finally {
        $base = null;
    }

?>