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
		//de los campos obligatorios, valida que los campos esten llenos
		$alert='';
		if(empty($_POST['proveedor']) || empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion']))
		{
			$alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$proveedor  = $_POST['proveedor'];
			$contacto     = $_POST['contacto'];
			$telefono  	= $_POST['telefono'];
			$direccion  = $_POST['direccion'];
			$usuario_id    = $_SESSION['idUser'];

			$result = 0;
			
			$query_insert = mysqli_query($conection,"INSERT INTO proveedor(proveedor,contacto,telefono,direccion,usuario_id)
																	VALUES ('$proveedor','$contacto','$telefono','$direccion','$usuario_id')");
				//validacion de insersion

				if($query_insert){
					$alert='<p class="msg_save">Proveedor registrado correctamente.</p>.';
				}else{
					$alert='<p class="msg_error">Error al registrar proveedor.</p>';
				}
		}		
		mysqli_close($conection);//cierre de conexion	
	}

 ?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
 	<meta charset="utf-8">
 	<?php include "includes/scripts.php"; ?>
 	<title>Registro Proveedor</title> 
 </head>
 <body>
 	<?php include "includes/header.php"; ?>
 	<section id="container">

 		<div class="form_register">
 			<h1><i class="fas fa-building"></i>  Registro Proveedor</h1>
 			<hr>
 			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

 			<form action="" method="post">
 				<label for="proveedor">Proveedor.</label>
 				<input type="text" name="proveedor" id="proveedor" placeholder="Nombre del Proveedor">
 				<label for="contacto">Contacto</label>
 				<input type="text" name="contacto" id="contacto" placeholder="Nombre completo del contacto">
 				<label for="telefono">Teléfono</label>
 				<input type="number" name="telefono" id="telefono" placeholder="Teléfono">
 				<label for="direccion">Direccion</label>
 				<input type="text" name="direccion" id="direccion" placeholder="Direccion completa">
 				 <button type="submit" class="btn_save"><i class="fas fa-save"></i> Guardar Proveedor</button>

 			</form>			

 		</div> 		

 	</section>
 	<?php include "includes/footer.php"; ?>
 </body>
 </html>