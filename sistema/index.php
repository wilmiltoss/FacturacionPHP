<?php 
	session_start();//direcciona al inicio segun el usuario logeado
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Sisteme Ventas</title>
</head>

<body>
	<?php 
	//incluimos el archivo de conexion
	include "../conexion.php";

	//modificacion de datos de la empresa en el index AJ.1
	//inicializamos las variables con valores vacios.
	$ci = '';
	$nombreEmpresa = '';
	$razonSocial = '';
	$telEmpresa = '';
	$emailEmpresa = '';
	$dirEmpresa = '';
	$iva = '';

	$query_empresa = mysqli_query($conection, "SELECT * FROM configuracion");
	$row_empresa   = mysqli_num_rows($query_empresa);
	if ($row_empresa > 0){//si la cantidad de filas obtenidas es mayor a 0
		//almacenamos en variable $arrInfoEmpresa por medio de mysqli_fetch_assoc
		while ($arrInfoEmpresa = mysqli_fetch_assoc($query_empresa)) {
				//asignamos a c/u de los valores a las variables de arriba
				$ci 		   = $arrInfoEmpresa['ci'];
				$nombreEmpresa = $arrInfoEmpresa['nombre'];
				$razonSocial   = $arrInfoEmpresa['razon_social'];
				$telEmpresa    = $arrInfoEmpresa['telefono'];
				$emailEmpresa  = $arrInfoEmpresa['email'];
				$dirEmpresa    = $arrInfoEmpresa['direccion'];
				$iva 		   = $arrInfoEmpresa['iva'];
		}
	}


	/*Ejecuta el procedimiento almacenado de contar cantidad de registros de c/ tabla p/ el index*/
	$query_dash	= mysqli_query($conection, "CALL dataDashboard();");
	$result_das	= mysqli_num_rows($query_dash);
	//guardamos en la variable $data_dash el array devuelto
	if ($result_das > 0){
		$data_dash  = mysqli_fetch_assoc($query_dash);
		mysqli_close($conection);
	}

	//prueba
	//print_r($data_dash);
	include "includes/header.php"; 

	?>
	<!--Contenedor del Panel de control-->
	<section id="container">
		<div class="divContainer">
				<div>
					<h1 class="titlePanelControl">Panel de control</h1>
				</div>
			<!--Opciones del Panel de control-->
			<div class="dashboard">
				<?php 
				//Validacion de mostrar/ocultar iconos index segun tipo usuarios
				if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){				
				 ?>
				<a href="lista_usuarios.php">
					<i class="fas fa-users"></i>
					<p>
						<strong>Usuarios</strong><br>
						<span><?=$data_dash['usuarios']; ?></span>
					</p>
				</a>
				<?php 
				}
				 ?>

				<a href="lista_clientes.php">
					<i class="fas fa-user"></i>
					<p>
						<strong>Clientes</strong><br>
						<span><?=$data_dash['clientes']; ?></span>
					</p>
				</a>
				<?php 
				//Validacion de mostrar/ocultar iconos index segun tipo usuarios
				if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){
				 ?>
				<a href="lista_proveedor.php">
					<i class="far fa-building"></i>
					<p>
						<strong>Proveedores</strong><br>
						<span><?=$data_dash['proveedores']; ?></span>
					</p>
				</a>
				<?php 
				}
				 ?>

				<a href="lista_producto.php">
					<i class="fas fa-cubes"></i>
					<p>
						<strong>Productos</strong><br>
						<span><?=$data_dash['productos']; ?></span>
					</p>
				</a>

				<a href="ventas.php">
					<i class="far fa-file-alt"></i>
					<p>
						<strong>Ventas del Dia</strong><br>
						<span><?=$data_dash['ventas']; ?></span>
					</p>
				</a>	
			</div>
		</div>

		<div class="divInfoSistem">
			<div>
				<h1 class="titlePanelControl">Configuracion</h1>
			</div>
			<div class="containerPerfil">
				<div class="containerDataUser">
					<div class="logoUser">
						<img src="img/logoUser.png">
					</div>
					<div class="divDataUser">
						<h4>Datos personales</h4>

						<div>
							<label>Nombre</label> <span><?= $_SESSION['nombre'];  ?></span>
						</div>
						<div>
							<label>Correo:</label> <span><?= $_SESSION['email'];  ?></span>
						</div>

						<h4>Datos Usuario</h4>
						<div>
							<label>Rol:</label> <span><?= $_SESSION['rol_name'];  ?></span>
						</div>
						<div>
							<label>Usuario:</label> <span><?= $_SESSION['user'];  ?></span>
						</div>

						<h4>Cambiar contraseña</h4> 
						<form action="" method="post" name="frmChangePass" id="frmChangePass"><!--boton confirmar contraseña AH.1-->
							<div>
								<input type="password" name="txtPassUser" id="txtPassUser" placeholder="Contraseña actual" required>
							</div>
								<input class="newPass" type="password" name="txtNewPassUser" id="txtNewPassUser" placeholder="Nueva contraseña" required>
							<div>
								<input class="newPass" type="password" name="txtPassConfirm" id="txtPassConfirm" placeholder="Confirmar contraseña" required>
							</div>
							<!--alerta de confirmacion de contraseña,  display: none para que no se muestre, solo se muestra con una condicion-->	
							<div class="alertChangePass" style="display: none;"><!--formato en css AG.1-->
								
							</div>
							<div>
								<button type="submit" class="btn_save btnChangePass"><i class="fas fa-key"></i> Cambiar contraseña</button>				
							</div>
						</form>

					</div>
				</div>
				<!--Ocultamos el formulario datos de la empresa a los que no son administradores-->
				<?php if ($_SESSION['rol'] == 1) { ?>
			
				<div class="containerDataEmpresa">
					<div class="logoEmpresa">
						<img src="img/logoEmpresa.png">
					</div>
					<h4>Datos de la empresa</h4><!--traemos los datos de las variables aca AJ.1-->
					 <form action="" method="post" name="frmEmpresa" id="frmEmpresa"><!--frmEmpresa le asignamos un evento en js AK.1-->
				 		<input type="hidden" name="action" value="updateDataEmpresa">
				 			<div>
				 				<label>C.I.:</label><input type="text" name="txtCI" id="txtCI" placeholder="CI de la empresa" value="<?= $ci; ?>" required>
				 			</div>
				 			<div>
				 				<label>Nombre:</label><input type="text" name="txtNombre" id="txtNombre" placeholder="Nombre de la empresa" value="<?= $nombreEmpresa; ?>" required>
				 			</div>
				 			<div>
				 				<label>Teléfono:</label><input type="text" name="txtTelEmpresa" id="txtTelEmpresa" placeholder="Número de telefono" value="<?= $telEmpresa; ?>" required>
				 			</div>
				 			<div>
				 				<label>Razon Social:</label><input type="text" name="txtRSocial" id="txtRSocial" placeholder="Razon Social" value="<?= $razonSocial; ?>">
				 			</div> 
				 			<div>
				 				<label>Correo electónico:</label> <input type="email" name="txtEmailEmpresa" id="txtEmailEmpresa" placeholder="Correo electónico" value="<?= $emailEmpresa; ?>" required>
				 			</div>
				 			<div>
				 				<label>Dirección:</label> <input type="text" name="txtDirEmpresa" id="txtDirEmpresa" placeholder="Dirección de la Empresa" value="<?= $dirEmpresa; ?>" required>
				 			</div>
				 			<div>
				 				<label>IVA (%):</label> <input type="text" name="txtIva" id="txtIva" placeholder="Impuesto al valor agregado (IVA)" value="<?= $iva; ?>" required>
				 			</div>
				 			<div class="alertFormEmpresa" style="display: none;"></div><!--display: none no va estar visible-->
				 			<div>
				 				<button type="submit" class="btn_save btnChangePass"> <i class="far fa-save fa-lg"></i> Guardar datos</button>
				 			</div>




					 </form>	
				</div>

			<?php } ?>
			</div>
		</div>



	</section>

	<?php include "includes/footer.php"; ?>

</body>
</html>