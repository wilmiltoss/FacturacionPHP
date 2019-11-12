<?php 

//VALIDACION FUNCION TIPO DE USUARIO LOGEADO
	session_start();
	if($_SESSION['rol'] != 1)
	{
		header("location: ../");
	}
	//*************************************

	include "../conexion.php";//detrocede un directorio

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Lista de Usuarios</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<?php 
			//RECIBIR LA VARIABLE DE BUSQUEDA, con el REQUEST se recibe y se guarda lo q viene de la url anterior

			$busqueda = strtolower($_REQUEST['busqueda']);
			if (empty($busqueda)) {
				header("location: lista_usuarios.php");
				mysqli_close($conection);//cierre de conexion
			}
		 ?>

		<h1> <i class="fas fa-address-book"></i> Lista de usuarios</h1>
		<a href="registro_usuario.php" class="btn_new">Crear usuario</a>
		
		<form action="buscar_usuario.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda; ?>">
		<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>

		<table>
			<tr>
				<th>ID</th>
				<th>Nombre</th>
				<th>Correo</th>
				<th>Usuario</th>
				<th>Rol</th>
				<th>Acciones</th>
			</tr>
			<?php 

				$rol = '';
				 if ($busqueda == 'administrador') 
				 {
				 	$rol = " OR rol LIKE '%1%'";

				 }else if ($busqueda == 'supervisor'){

				 	$rol = " OR rol LIKE '%2%'";

				 }else if ($busqueda == 'vendedor'){

				 	$rol = " OR rol LIKE '%3%'"; 
				 }
				//CONTROL DE PAGINADOR
				$sql_registe = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM usuario
																	  WHERE (idusuario LIKE '%$busqueda%' OR
																	  		 nombre LIKE '%$busqueda%' OR
																	  		 correo LIKE '%$busqueda%' OR
																	  		 usuario LIKE '%$busqueda%'
																	  		 $rol )
																	  AND estatus = 1 ");



				$result_register = mysqli_fetch_array($sql_registe);
				$total_registro = $result_register['total_registro'];

				//muestra la cantidade registros por pagina
				$por_pagina = 10; 

				if(empty($_GET['pagina']))
				{
					$pagina = 1;
				}else{
					$pagina = $_GET['pagina'];
				}

				$desde = ($pagina-1) * $por_pagina;
				$total_paginas = ceil($total_registro / $por_pagina); //ceil redondea los nros.

				//consulta de usuarios activos// busqueda 
				$query = mysqli_query($conection, "SELECT u.idusuario, u.nombre, u.correo, u.usuario, r.rol FROM usuario u INNER JOIN rol r ON u.rol = r.idrol WHERE (u.idusuario LIKE '%$busqueda%' OR
									  		 u.nombre LIKE '%$busqueda%' OR
									  		 u.correo LIKE '%$busqueda%' OR
									  		 u.usuario LIKE '%$busqueda%' OR
									  		 r.rol  LIKE '%$busqueda%' )
									AND
									estatus = 1 ORDER BY u.nombre ASC LIMIT $desde,$por_pagina
					");
				mysqli_close($conection);//cierre de conexion
				$result = mysqli_num_rows($query);
				if($result > 0){
					while ($data = mysqli_fetch_array($query)) {

				?>	
					<tr>
						<td><?php echo $data['idusuario']; ?></td>
						<td><?php echo $data['nombre']; ?></td>
						<td><?php echo $data['correo']; ?></td>
						<td><?php echo $data['usuario']; ?></td>
						<td><?php echo $data['rol']; ?></td>
						<td>
							<a class="link_edit" href="editar_usuario.php?id=<?php echo $data['idusuario']; ?>"><i class="fas fa-edit"></i> Editar</a>
							<?php if($data["idusuario"] != 1) { ?>
							|
							<a class="link_delete" href="eliminar_confirmar_usuario.php?id=<?php echo $data['idusuario']; ?>"><i class="fas fa-trash-alt"></i> Eliminar</a>
						<?php } ?>
						</td>
					</tr>

				<?php 
						
					}
				}

			 ?>

			

		</table>
		<?php 
			if($total_registro != 0)
			{
		 ?>


		<div class="paginador">
			<ul>
				<?php 
				//**********************BOTONES PAGINADOR******************************************
				//BLOQUEAR EL BOTON ULTIMA O 1RA PAGINA
				if ($pagina != 1) {

				 ?>
			    <li><a href="?pagina=<?php echo 1; ?>"><i class="fas fa-step-backward"></i></a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>"><i class="fas fa-caret-left fa-sm"></i></a></li>li>

				<?php 					
					}
				//PARA PINTAR EL CUADRO DE ENLACE
				for ($i=1; $i <=  $total_paginas; $i++) { 
					if ($i == $pagina) {
						echo '<li class ="pageSelected">'.$i.'</li>';
					}else{

					echo '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>';

					}
				}
				if ($pagina != $total_paginas) 
				{
				 ?>
				
				<li><a href="?pagina=<?php echo $pagina + 1; ?>"><i class="fas fa-caret-right fa-sm"></i></a></li>
				<li><a href="?pagina=<?php echo $total_paginas; ?>"><i class="fas fa-step-forward "></i></a></li>
				<?php 
					}
				 ?>
			</ul>
		</div>
		<?php } ?>
	</section>

	<?php include "includes/footer.php"; ?>

</body>
</html>