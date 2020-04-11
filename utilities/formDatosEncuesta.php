<div class="row p-3 my-3">
    <h3>Datos de la encuesta</h3>
</div>
<div class="row p-3 my-1">
    <form action="gestion.php" method="POST" class="form-inline">
        <div class="form-group">
            <label for="encTit">Título: </label>
            <input type="text" name="encTitTxt" id="encTit" class="form-control" <?php echo 'value="' . $descripcion . '"'; ?>>
            <label for="encIns">Descripción: </label>
            <textarea name="encInsTxt" id="encIns" class="form-control">
                    <?php
                    echo $instrucciones;
                    ?>
                </textarea>
            <input type="submit" name="subEditEnc" value="Aplicar cambios" class="btn btn-primary">
        </div>
    </form>
</div>