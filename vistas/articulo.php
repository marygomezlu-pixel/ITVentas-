<?php
require_once "header.php";
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-cubes"></i> Gestión de Artículos</h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Registro de artículos</h3>
                <button class="btn btn-primary pull-right" onclick="mostrarFormulario(true)">
                    <i class="fa fa-plus-circle"></i> Nuevo artículo
                </button>
            </div>

            <div class="box-body" id="listadoArticulos">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>N.º</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Precio compra</th>
                                <th>Precio venta</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaArticulos">
                            <tr><td colspan="9" class="text-center">Cargando artículos...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box-body" id="formularioArticulo" style="display:none;">
                <form id="formArticulo">
                    <input type="hidden" name="idarticulo" id="idarticulo">

                    <div class="form-group">
                        <label>Categoría</label>
                        <select name="idcategoria" id="idcategoria" class="form-control" required></select>
                    </div>

                    <div class="form-group">
                        <label>Código</label>
                        <input type="text" name="codigo" id="codigo" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Nombre del artículo</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Stock inicial</label>
                        <input type="number" name="stock" id="stock" class="form-control" min="0" value="0" required>
                    </div>

                    <div class="form-group">
                        <label>Precio de compra</label>
                        <input type="number" step="0.01" name="precio_compra" id="precio_compra" class="form-control" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Precio de venta</label>
                        <input type="number" step="0.01" name="precio_venta" id="precio_venta" class="form-control" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Guardar artículo
                    </button>
                    <button type="button" class="btn btn-default" onclick="mostrarFormulario(false)">
                        Cancelar
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>
<?php require_once "footer.php"; ?>
