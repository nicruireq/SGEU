<div class="row p-3 my-3">
    <h3>Categorias</h3>
</div>
<div class="row p-3 my-1">
    <form action="gestion.php" method="POST" class="form-inline">
        <label for="catNom">Nombre: </label>
        <input type="text" name="catNomTxt" id="catNom" class="form-control" value="Nombre de la nueva categoría...">
        <input type="submit" name="subNewCat" value="Nueva" class="btn-sm btn-primary">
    </form>
</div>
<div class="row p-3 my-1">
    <?php
    if ($error && $errores['formNomCategoria'] != "") {
        msg_alert('formNomCategoria');
    }
    ?>
</div>
<div class="row p-3 my-1">
    <form action="gestion.php" method="POST" class="form-inline">
        <label for="catSel">Selecciona categoria: </label>
        <select name="catSel" id="catSel" class="form-control">
            <option value="">Seleccione categoria para editar</option>
            <?php
                for ($i=0; $i < count($categorias); $i++) { 
                    echo '<option value="'.$i.'">'.$categorias[$i]['NombreCat'].'</option>';
                }
            ?>
        </select>
        <div class="btn-group btn-group-sm">
            <input type="submit" name="subEditCat" value="Editar" class="btn-sm btn-primary">
            <input type="submit" name="subDelCat" value="Eliminar" class="btn-sm btn-danger">
            <input type="submit" name="subSelCat" value="Marcar" class="btn-sm btn-warning">
        </div>
    </form>
</div>
<div class="row p-3 my-1">
    <?php
    if ($error && $errores['formEditCategoria'] != "") {
        msg_alert('formEditCategoria');
    }
    ?>
</div>
<?php
    if ($_SESSION['showEditboxCategoria']) {
        echo '<div class="row p-3 my-1">';
        echo '<form action="gestion.php" method="POST" class="form-inline">';
        echo '<label for="catEditNom">Nuevo nombre: </label>';
        echo '<input type="text" name="catEditNom" id="catEditNom" class="form-control">';
        echo '<input type="submit" name="subApplyCat" value="Aplicar cambios" class="btn-sm btn-primary">';
        echo '</form>';
        echo '</div><div class="row p-3 my-1">';
        if ($error && $errores['formApplyCatNom'] != "") {
            msg_alert('formApplyCatNom');
        }
        echo '</div';
    }
?>
<div class="row p-3 my-1">
    <?php
    /*
    if ($error && $errores['formTitEncuesta'] != "") {
        msg_alert('formTitEncuesta');
    }
    if ($error && $errores['formInsEncuesta'] != "") {
        msg_alert('formInsEncuesta');
    }
    */
    ?>
</div>