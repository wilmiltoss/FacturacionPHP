<?php 
	session_start();

	include "../conexion.php";

	if(!empty($_POST))
	{
		//de los campos obligatorios
		$alert='';
		if(empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion']))
		{
			$alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$nit	    = $_POST['nit'];
			$nombre     = $_POST['nombre'];
			$telefono  	= $_POST['telefono'];
			$direccion  = $_POST['direccion'];
			$usuario    = $_SESSION['idUser'];

			$result = 0;

			//no permitr el ingreso de duplicado de cedula, si el nit es diferente de 0 valida el select
			if (is_numeric($nit) and $nit !=0) 
			{
				$query = mysqli_query($conection,"SELECT * FROM Cliente WHERE nit = '$nit'  ");
				$result = mysqli_fetch_array($query);
			}
			//si no existe el nro de cedula, va insertar los datos del cliente
			if ($result > 0) {
				$alert = '<p class="msg_error">El numero de Cedula ya existe.</p>';
			}else{

				$query_insert = mysqli_query($conection,"INSERT INTO cliente(nit,nombre,telefono,direccion,usuario_id)
																	VALUES ('$nit','$nombre','$telefono','$direccion','$usuario')");
				//validacion de insersion

				if($query_insert){
					$alert='<p class="msg_save">Cliente registrado correctamente.</p>.';
				}else{
					$alert='<p class="msg_error">Error al registrar cliente.</p>';
				}
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
 	<title>Registro de Cliente</title> 
 </head>
 <body>
 	<?php include "includes/header.php"; ?>
 	<section id="container">

 		<div class="form_register">
 			<h1><i class="fas fa-user-plus"></i>  Registro de cliente</h1>
 			<hr>
 			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

 			<form action="" method="post">
 				<label for="nit">C.I.</label>
 				<input type="number" name="nit" id="nit" placeholder="Número de Cédula">
 				<label for="nombre">Nombre</label>
 				<input type="text" name="nombre" id="nombre" placeholder="Nombre completo">
 				<label for="telefono">Teléfono</label>
 				<input type="number" name="telefono" id="telefono" placeholder="Teléfono">
 				<label for="direccion">Direccion</label>
 				<input type="text" name="direccion" id="direccion" placeholder="Direccion completa">
 				 <button type="submit" class="btn_save"><i class="fas fa-save"></i> Guardar Cliente</button>

 			</form>			

 		</div> 		

 	</section>
 	<?php include "includes/footer.php"; ?>
 </body>
 </html>