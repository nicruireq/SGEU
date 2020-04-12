<div class="row p-3 my-3">
    <h3>Preguntas</h3>
</div>
<!-- primero tiene que haberse marcado una categoria para ver sus preguntas -->
<?php if (empty($_SESSION['idCategoriaSelected'])): ?>
    <div class="alert alert-warning">
        Debe seleccionar una categoria para ver sus preguntas, también puede seleccionar una subcategoria
    </div>
<?php else: ?>
<!--Mostrar vista a partir de aqui-->
    <?php if( !empty($_SESSION['idSubcategoriaSelected']) && !empty($_SESSION['idCategoriaSelected']) ): ?>
        <div class="alert alert-info">
            Esta viendo preguntas para la categoria: <?php echo $_SESSION['nomCategoriaSelected']; ?> y subcategoria: <?php echo $_SESSION['nomSubcategoriaSelected']; ?>
        </div>
    <?php elseif( !empty($_SESSION['idCategoriaSelected']) ): ?>
        <div class="alert alert-info">
            Esta viendo preguntas para la categoria: <?php echo $_SESSION['nomCategoriaSelected']; ?>
        </div>
    <?php endif; ?>
<!-- formulario de edicion y eliminacion -->
<div class="row p-3 my-1">
    <form action="gestion.php" method="POST" class="form-inline">
        <label for="pregSel">Selecciona subcategoria: </label>
        <select name="pregSel" id="pregSel" class="form-control">
            <option value="">Seleccione pregunta para editar</option>
            <?php
                for ($i=0; $i < count($preguntas); $i++) { 
                    echo '<option value="'.$i.'">'.$preguntas[$i]['Enunciado'].'</option>';
                }
            ?>
        </select>
        <div class="btn-group btn-group-sm">
            <input type="submit" name="subEditPreg" value="Editar" class="btn-sm btn-primary">
            <input type="submit" name="subDelPreg" value="Eliminar" class="btn-sm btn-danger">
            <input type="submit" name="subNewPreg" value="Nueva" class="btn-sm btn-primary">
        </div>
    </form>
</div>
<div class="row p-3 my-1">
    <?php
    if ($error && $errores['formEditPreg'] != "") {
        msg_alert('formEditPreg');
    }
    ?>
</div>
<!-- formulario de edicion de pregunta o crear nueva -->
<?php if ($_SESSION['showEditFormPregunta']): ?>
    <div>EDITAR PREGUNTA</div>
<?php elseif ($_SESSION['showNewFormPregunta']): ?>
    <div class="row p-3 my-1">
    <form action="gestion.php" method="POST">
        <div class="form-group">
            <label for="enunciado">Enunciado: </label>
            <textarea name="enunciado" id="enunciado" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <input type="checkbox" name="relprof" value="Relacionada con profesores">
        </div>
        <div class="form-group" id="formOptions">
            <button type="button" onclick="addInputOption()" class="btn-sm btn-primary">Añadir opción</button>
        </div>
        <input type="submit" name="subSaveNewPreg" value="Guardar" class="btn-sm btn-primary">
    </form>
    </div>
<?php endif; ?>

<!-- formulario creacion nueva pregunta -->


<!-- fin vista -->
<?php endif; ?>

<!--script para las opciones-->
<script>
    var cont = 0;
    function addInputOption() {

    }
</script>