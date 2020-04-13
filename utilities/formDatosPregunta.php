<div class="row p-3 my-3">
    <h3>Preguntas</h3>
</div>
<!-- primero tiene que haberse marcado una categoria para ver sus preguntas -->
<?php if (empty($_SESSION['idCategoriaSelected'])) : ?>
    <div class="alert alert-warning">
        Debe seleccionar una categoria para ver sus preguntas, también puede seleccionar una subcategoria
    </div>
<?php else : ?>
    <!--Mostrar vista a partir de aqui-->
    <?php if (!empty($_SESSION['idSubcategoriaSelected']) && !empty($_SESSION['idCategoriaSelected'])) : ?>
        <div class="alert alert-info">
            Esta viendo preguntas para la categoria: <?php echo $_SESSION['nomCategoriaSelected']; ?> y subcategoria: <?php echo $_SESSION['nomSubcategoriaSelected']; ?>
        </div>
    <?php elseif (!empty($_SESSION['idCategoriaSelected'])) : ?>
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
                for ($i = 0; $i < count($preguntas); $i++) {
                    echo '<option value="' . $i . '">' . $preguntas[$i]['Enunciado'] . '</option>';
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
    <?php if ($_SESSION['showEditFormPregunta']) : ?>
        <div>EDITAR PREGUNTA - NO IMPLEMENTADO</div>
    <?php elseif ($_SESSION['showNewFormPregunta']) : ?>
        <div class="row p-3 my-1">
            <form action="gestion.php" method="POST" id="formNewPreg">
                <div class="form-group">
                    <label for="enunciado">Enunciado: </label>
                    <textarea name="enunciado" id="enunciado" class="form-control"></textarea>
                </div>
                <div class="row p-3 my-1">
                    <?php
                    if ($error && $errores['enunciadoEmpty'] != "") {
                        msg_alert('enunciadoEmpty');
                    }
                    ?>
                </div>
                <div class="form-group">
                    <label for="relprof">Relacionada con profesores? </label>
                    <input type="checkbox" name="relprof" id="relprof" value="relprof">
                </div>
                <div class="form-group" id="formOptions">
                    <button type="button" onclick="addInputOption()" class="btn-sm btn-primary">Añadir opción</button>
                </div>
                <div class="row p-3 my-1">
                    <?php
                    if ($error && $errores['noOptions'] != "") {
                        msg_alert('noOptions');
                    }
                    ?>
                </div>
                <input type="button" name="subSaveNewPreg" onclick="submitNewPreg()" value="Guardar" class="btn-sm btn-primary">
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
        ++cont;
        parent = document.getElementById("formOptions");
        txtOp = document.createElement("input");
        txtOp.type = "text";
        txtOp.className = "form-control";
        txtOp.name = "op" + cont;
        btnRem = document.createElement("button");
        btnRem.type = "button";
        btnRem.className = "btn-sm btn-danger";
        btnRem.setAttribute("onclick", "removeInputOption(" + cont + ")");
        btnRem.id = "remOp" + cont;
        btnRem.innerText = "Quitar";
        parent.appendChild(txtOp);
        parent.appendChild(btnRem);
    }

    function removeInputOption(op) {
        parent = document.getElementById("formOptions");
        // para reasignar los nombres de los inputs de las opciones
        // correctamente cada vez que se elimina una opcion
        for (let i = op; i <= cont; i++) {
            childTxt = document.getElementsByName("op" + i)[0];
            childBut = document.getElementById("remOp" + i);
            // el primero se elimina
            if (i == op) {
                parent.removeChild(childTxt);
                parent.removeChild(childBut);
            } else {
                // los siguientes son reasignados
                childTxt.setAttribute("name", "op" + (i - 1));
                childBut.setAttribute("id", "remOp" + (i - 1));
                childBut.setAttribute("onclick", "removeInputOption(" + (i - 1) + ")");
            }
        }
        // actualizar numero de opciones
        --cont;
    }

    function submitNewPreg() {
        // crear input oculto para pasar el numero de opciones
        // que se han creado
        inputhidden = document.createElement("input");
        inputhidden.type = "hidden";
        inputhidden.name = "numOps";
        inputhidden.value = cont;
        form = document.getElementById("formNewPreg");
        form.appendChild(inputhidden);
        // enviar formulario
        form.submit();
    }
</script>