<?php 

//VALIDACION FUNCION TIPO DE USUARIO LOGEADO
	session_start();
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] !=2)
	{
		header("location:../");
	}
	include "../conexion.php";//detrocede un directorio

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Lista de Clientes</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<?php 
			//RECIBIR LA VARIABLE DE BUSQUEDA, con el REQUEST se recibe y se guarda lo q viene de la url anterior

			$busqueda = strtolower($_REQUEST['busqueda']);
			if (empty($busqueda)) {
				header("location: lista_proveedor.php");
				mysqli_close($conection);//cierre de conexion
			}
		 ?>

		<h1><i class="fas fa-building"></i> Lista de Proveedores</h1>
		<a href="registro_proveedor.php" class="btn_new"><i class="fas fa-plus"></i> Crear proveedor</a>
		
		<form action="buscar_proveedor.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda; ?>">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>

		<table>
			<tr>
				<th>ID </th>
				<th>Proveedor</th>
				<th>Contacto</th>
				<th>Teléfono</th>
				<th>Dirección</th>
				<th>Fecha</th>
				<th>Acciones</th>
			</tr>
			<?php 

				//CONTROL DE PAGINADOR
				$sql_registe = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM proveedor
																	  WHERE (codproveedor LIKE '%$busqueda%' OR								
																	  		 proveedor LIKE '%$busqueda%' OR
																	  		 contacto LIKE '%$busqueda%' OR
																	  		 telefono LIKE '%$busqueda%')														  	
																	  AND estatus = 1 ");

				

				$result_register = mysqli_fetch_array($sql_registe);
				$total_registro = $result_register['total_registro'];

				//muestra la cantidade registros por pagina
				$por_pagina = 2; 

				if(empty($_GET['pagina']))
				{
					$pagina = 1;
				}else{
					$pagina = $_GET['pagina'];
				}

				$desde = ($pagina-1) * $por_pagina;
				$total_paginas = ceil($total_registro / $por_pagina); //ceil redondea los nros.

				//consulta de usuarios activos// busqueda 
				$query = mysqli_query($conection, "SELECT * FROM proveedor 
													WHERE (codproveedor LIKE '%$busqueda%' OR
											  		 proveedor LIKE '%$busqueda%' OR
											  		 contacto LIKE '%$busqueda%' OR
											  		 telefono LIKE '%$busqueda%' )
													AND
													estatus = 1 ORDER BY codproveedor ASC LIMIT $desde,$por_pagina
					");
				mysqli_close($conection);//cierre de conexion

				$result = mysqli_num_rows($query);
				if($result > 0){
					while ($data = mysqli_fetch_array($query)) {
						 $formato = 'Y-m-d H:i:s';
						 $fecha  = Datetime::createFromFormat($formato,$data["date_add"]);

				?>	
					<tr>
						<td><?php echo $data['codproveedor']; ?></td>
						<td><?php echo $data['proveedor']; ?></td>
						<td><?php echo $data['contacto']; ?></td>
						<td><?php echo $data['telefono']; ?></td>
						<td><?php echo $data['direccion']; ?></td>
						<td><?php echo $fecha->format('d-m-Y'); ?></td>

						<td>
							<a class="link_edit" href="editar_proveedor.php?id=<?php echo $data['codproveedor']; ?>"> <i class="fas fa-edit"></i> Editar</a>
							|
							<a class="link_delete" href="eliminar_confirmar_proveedor.php?id=<?php echo $data['codproveedor']; ?>"> <i class="fas fa-trash-alt"></i> Eliminar</a>
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