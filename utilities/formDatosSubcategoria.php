<div class="row p-3 my-3">
    <h3>Subcategorias</h3>
</div>
<!-- primero tiene que haberse marcado una categoria para ver subcategorias -->
<?php if (empty($_SESSION['idCategoriaSelected'])): ?>
    <div class="alert alert-warning">
        Debe seleccionar una categoria para ver sus subcategorias
    </div>
<?php else: ?>
    <div class="alert alert-info">
        Esta viendo datos para la categoria: <?php echo $_SESSION['nomCategoriaSelected']; ?>
    </div>
<?php endif; ?>
<!--Empieza el formulario para subcategorias-->
<div class="row p-3 my-1">
    <form action="gestion.php" method="POST" class="form-inline">
        <label for="subcatNom">Nombre: </label>
        <input type="text" name="subcatNomTxt" id="subcatNom" class="form-control" value="Nombre de la nueva subcategoría...">
        <input type="submit" name="subNewSubcat" value="Nueva" class="btn-sm btn-primary">
    </form>
</div>
<div class="row p-3 my-1">
    <?php
    if ($error && $errores['formNomSubcategoria'] != "") {
        msg_alert('formNomSubcategoria');
    }
    ?>
</div>
<div class="row p-3 my-1">
    <form action="gestion.php" method="POST" class="form-inline">
        <label for="subcatSel">Selecciona subcategoria: </label>
        <select name="subcatSel" id="subcatSel" class="form-control">
            <option value="">Seleccione subcategoria para editar</option>
            <?php
                for ($i=0; $i < count($subcategorias); $i++) { 
                    echo '<option value="'.$i.'">'.$subcategorias[$i]['NombreSub'].'</option>';
                }
            ?>
        </select>
        <div class="btn-group btn-group-sm">
            <input type="submit" name="subEditSubcat" value="Editar" class="btn-sm btn-primary">
            <input type="submit" name="subDelSubcat" value="Eliminar" class="btn-sm btn-danger">
            <input type="submit" name="subSelSubcat" value="Marcar" class="btn-sm btn-warning">
        </div>
    </form>
</div>
<div class="row p-3 my-1">
    <?php
    if ($error && $errores['formEditSubcategoria'] != "") {
        msg_alert('formEditSubcategoria');
    }
    ?>
</div>
<?php
    if ($_SESSION['showEditboxSubcategoria']) {
        echo '<div class="row p-3 my-1">';
        echo '<form action="gestion.php" method="POST" class="form-inline">';
        echo '<label for="subcatEditNom">Nuevo nombre: </label>';
        echo '<input type="text" name="subcatEditNom" id="subcatEditNom" class="form-control">';
        echo '<input type="submit" name="subApplySubCat" value="Aplicar cambios" class="btn-sm btn-primary">';
        echo '</form>';
        echo '</div><div class="row p-3 my-1">';
        if ($error && $errores['formApplySubcatNom'] != "") {
            msg_alert('formApplySubcatNom');
        }
        echo '</div';
    }
?>
