
<?php
require_once "header.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-tags"></i> Gestión de Categorías</h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Registro de categorías</h3>
                <button class="btn btn-primary pull-right" id="btnAgregar" onclick="mostrarFormulario(true)">
                    <i class="fa fa-plus-circle"></i> Nueva categoría
                </button>
            </div>

            <div class="box-body" id="listadoCategorias">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>N.º</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaCategorias">
                            <tr>
                                <td colspan="5" class="text-center">Cargando categorías...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box-body" id="formularioCategoria" style="display:none;">
                <form id="formCategoria">
                    <input type="hidden" id="idcategoria" name="idcategoria">

                    <div class="form-group">
                        <label>Nombre de la categoría</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-default" onclick="mostrarFormulario(false)">
                        Cancelar
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>


<?php
require_once "footer.php";
?>
