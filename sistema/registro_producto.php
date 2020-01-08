<?php 
	session_start();

	if($_SESSION['rol'] != 1 and $_SESSION['rol'] !=2)
	{
		header("location:../");
	}
	include "../conexion.php";

	//para el boton guardar proveedor
	if(!empty($_POST))
	{
		//print_r($_FILES); // Tipo Files obtiene los datos del archivo cargado
		//exit;
		//de los campos obligatorios, valida que los campos esten llenos
		$alert='';
		if(empty($_POST['proveedor']) || empty($_POST['producto']) || empty($_POST['precio']) || $_POST['precio'] <= 0 ||empty($_POST['cantidad'] || $_POST['cantidad'] <= 0))
		{
			$alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$proveedor  = $_POST['proveedor'];
			$producto   = $_POST['producto'];
			$precio     = $_POST['precio'];
			$cantidad   = $_POST['cantidad'];
			$usuario_id = $_SESSION['idUser'];
 
			$foto        = $_FILES['foto'];
			$nombre_foto = $foto['name'];
			$type        = $foto['type'];
			$url_temp    = $foto['tmp_name'];
 
 			//cuando no lleva nombre la foto se almacena por defecto el nombre img_producto.png con el nombre aleatorio
			$imgProducto = 'img_producto.png';

			if ($nombre_foto != '')
			 {//guardamos la foto con el nombre de la fecha hora para no duplicarlos en la carpeta img/uploads
				$destino = 'img/uploads/';
				$img_nombre = 'img_'.md5(date('d-m-Y H:m:s')); //encriptamos la fecha p/ genrar el nombre aleatorio
				$imgProducto = $img_nombre.'.jpg'; //concatenamos la extencion del archivo
				$src 		 = $destino.$imgProducto; //guardamos el nombre en la variable concatenado en el destino
			}

			
			$query_insert = mysqli_query($conection,"INSERT INTO producto(proveedor,descripcion,precio,existencia,usuario_id,foto)
																	VALUES ('$proveedor','$producto','$precio','$cantidad','$usuario_id','$imgProducto')");
				//validacion de insersion

				if($query_insert){
					if ($nombre_foto != '') {
						//almacena en la ruta temporal y la mueve en el nuevo destino $src
						move_uploaded_file($url_temp, $src);
					}
					$alert='<p class="msg_save">Producto registrado correctamente.</p>.';
				}else{
					$alert='<p class="msg_error">Error al registrar producto.</p>';
				}
		}		
	}

 ?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
 	<meta charset="utf-8">
 	<?php include "includes/scripts.php"; ?>
 	<title>Registro Producto</title> 
 </head>
 <body>
 	<?php include "includes/header.php"; ?>
 	<section id="container">

 		<div class="form_register">
 			<h1><i class="fas fa-cubes"></i>  Registro Producto</h1>
 			<hr>
 			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
 			<!-- multipart utilizado p/ adjuntar archivos-->
 			<form action="" method="post" enctype="multipart/form-data">

 				<label for="proveedor">Proveedor</label>
 				<?php 
 					$query_proveedor = mysqli_query($conection, "SELECT codproveedor, proveedor FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC");
 					$result_proveedor = mysqli_num_rows($query_proveedor);
 					mysqli_close($conection);
 				 ?>
 				 <!-- LISTA DESPLEGABLE DEL PROVEEDOR-->
 				<select name="proveedor" id="proveedor">
 					<?php 
 						if ($result_proveedor > 0) {

 							while ($proveedor = mysqli_fetch_array($query_proveedor)) { 								
 						?>
 							<option value="<?php echo $proveedor['codproveedor']; ?>"><?php echo $proveedor['proveedor']; ?></option>
 						<?php 

 							}
 						}
 					?>	
 				</select>

 				<label for="producto">Producto</label>
 				<input type="text" name="producto" id="producto" placeholder="Nombre completo del producto">
 				<label for="precio">Precio</label>
 				<input type="number" name="precio" id="precio" placeholder="Precio del producto">
 				<label for="cantidad">Cantidad</label>
 				<input type="number" name="cantidad" id="cantidad" placeholder="Cantidad del producto">
 				<!-- carga las fotos-->
				<div class="photo">
					<label for="foto">Foto</label>
				        <div class="prevPhoto">
				        <span class="delPhoto notBlock">X</span>
				        <label for="foto"></label>
				        </div>
				        <div class="upimg">
				        <input type="file" name="foto" id="foto">
				        </div>
				        <div id="form_alert"></div>
				</div>



 				 <button type="submit" class="btn_save"><i class="fas fa-save"></i> Guardar Producto</button>

 			</form>			

 		</div> 		

 	</section>
 	<?php include "includes/footer.php"; ?>
 </body>
 </html>