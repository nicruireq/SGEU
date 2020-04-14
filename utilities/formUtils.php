<?php

    // html para mensaje de alerta
    function msg_alert($index) {
        $err = $GLOBALS['errores'];
        echo '<div class="alert alert-danger">' .
                $err[$index] . '</div>';
    }