<?php 
	//la sesion debe estar iniciada para mostrar la pagina
	session_start();
	include "../conexion.php";
	//echo md5($_SESSION['idUser']);//prueba generar tocken con id del usuario
 ?>

<!DOCTYPE html>
<html lang="en">
<!-- scripts.php de las bibliotecas de js-->
<?php include "includes/scripts.php"; ?>
<head>
	<meta charset="utf-8">
	<title>Nueva Venta</title>
</head>
<body>
	<!--header.php es el que tiene el menu de navegacion(encabezado) y la validacion si estamos logeados o no -->
	<?php include "includes/header.php"; ?>

	<!--HOJA DE TRABAJO-->
	<section id="container">
		<div class="title_page">
			<h1><i class="fas fa-cube"></i> Nueva venta</h1>
		</div>	
		<div class="datos_cliente">
			<div class="action_cliente">
				<h4>Datos del Cliente</h4><!--Q.1 remosion de los atributos disable = habilitar campos-->
				<a href="#" class="btn_new btn_new_cliente"><i class="fas fa-plus"></i> Nuevo cliente</a>
			</div>
			<!--FORMULARIO NUEVA VENTA-->
			<!--Crear nuevo cliente T.1-->
			<form name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos">
				<input type="hidden" name="action" value="addCliente"> <!-- addCliente U.1-->
				<input type="hidden" name="idcliente" id="idcliente" value="" required>
				<div class="wd30"><!--wd30 para los css-->
					<label>C.I.</label>
					<input type="text" name="ci_cliente" id="ci_cliente">
				</div>
				<div class="wd30">
					<label>Nombre</label>
					<input type="text" name="nom_cliente" id="nom_cliente" disabled required>		
				</div>
				<div class="wd30">
					<label>Teléfono</label> 
					<input type="number" name="tel_cliente" id="tel_cliente" disabled required><!--disabled = txt desactivado-->		
				</div>
				<div class="wd100">
					<label>Dirección</label>
					<input type="text" name="dir_cliente" id="dir_cliente" disabled required>		
				</div>
				<div id="div_registro_cliente" class="wd100">
					<!--R.1 mostramos el boton div_registro_cliente-->
					<button type="submit" class="btn_save"><i class="far fa-save fa-lg"></i> Guardar</button>
					
				</div>		
			</form>
		</div>
		<div class="datos_venta">
			<h4>Datos Venta</h4>
			<div class="datos">
				<div class="wd50"><!--wd50 significa 50% de ancho-->
					<label>Vendedor</label>
					<!--mostramos nombre con la variable se session-->	
					<p><?php echo $_SESSION['nombre']; ?></p>
				</div> 
				<div class="wd50">
					<label>Acciones</label>
					<div id="acciones_venta">
						<a href="#" class="btn_ok textcenter" id="btn_anular_venta"><i class="fas fa-ban"></i> Anular</a>
						<a href="#" class="btn_new textcenter" id="btn_facturar_venta" style="display: none;"><i class="far fa-edit"></i> Procesar</a> <!--style="display: none; hace que no se muestre al momento de cargar la pagina-->
						
					</div>
				</div>

		   </div>	
		</div>
		<table class="tbl-venta">
			<thead>

				<tr><!--Fila de la cabecera-->
					<th width="100px">Código</th>
					<th>Descripción</th>
					<th>Existencia</th>
					<th width="100px">Cantidad</th>
					<th class="textright">Precio</th>
					<th class="textright">Precio total</th>
					<th> Acción</th>
				</tr>

				<tr> <!--Fila de los detalles-->
					<td><input type="text" name="txt_cod_producto" id="txt_cod_producto"></td><!--BUSCAR PRODUCTO U.1-->
					<td id="txt_descripcion">-</td>
					<td id="txt_existencia">-</td>
					<td><input type="text" name="txt_cant_producto" id="txt_cant_producto" value="0" min="1" disabled></td>
					<td id="txt_precio" class="textright">0.00</td>
					<td id="txt_precio_total" class="textright">0.00</td>
					<td><a href="#" id="add_product_venta" class="link_add"><i class="fas fa-plus"></i> Agregar</a></td><!-- add_product_venta Agregar producto V.1-->
				</tr>
				<tr>
					<th>Codigo</th>
					<th colspan="2">Descripción</th>
					<th>Cantidad</th>
					<th class="textright">Precio</th>
					<th class="textright">Precio Total</th>
					<th>Acción</th>
				</tr>
			</thead>
			<!--X.1 corresponde al contenido AJAX, lo recibimos desde ajax.php-->
			<tbody id="detalle_venta">
			</tbody>
			<!--X.1 corresponde al contenido AJAX, lo recibimos desde ajax.php-->
			<tfoot id="detalle_totales">
			</tfoot>
		</table>
	</section>
	
	<!--footer.php contiene el estilo del menu css-->
	<?php include "includes/footer.php"; ?>

	<!--Funcion para mantener la pagina 'nueva venta' cargada sin eliminar  al pasar a otra pestaña o refrescar-->
	<script type="text/javascript">
		$(document).ready(function(){
			var usuarioid = '<?php echo $_SESSION['idUser']; ?>';//variable de sesion como parametro
			serchForDetalle(usuarioid);//mantenemos la ventana activa con la sesion de variable con la funcion serchForDetalle Z.1
		});
	</script>


</body>
</html>
