<div class="row p-3 my-3">
    <h3>Datos de la encuesta</h3>
</div>
<div class="row p-3 my-1">
    <form action="gestion.php" method="POST" class="form-inline">
        <div class="form-group">
            <label for="encTit">Título: </label>
            <input type="text" name="encTitTxt" id="encTit" class="form-control" <?php echo 'value="' . $_SESSION['descripcion'] . '"'; ?>>
            <label for="encIns">Descripción: </label>
            <textarea name="encInsTxt" id="encIns" class="form-control"><?php echo $_SESSION['instrucciones']; ?></textarea>
            <input type="submit" name="subEditEnc" value="Aplicar cambios" class="btn btn-primary">
        </div>
    </form>
</div>
<div class="row p-3 my-1">
    <?php
    if ($error && $errores['formTitEncuesta'] != "") {
        msg_alert('formTitEncuesta');
    } 
    if ($error && $errores['formInsEncuesta'] != "") {
        msg_alert('formInsEncuesta');
    }
    ?>
</div>