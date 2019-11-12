<?php 
	session_start();//inicia el navegador
	include "../conexion.php";//detrocede un directorio

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Lista de Cliente</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">

		<h1><i class="fas fa-address-book"></i> Lista de Clientes</h1>
		<a href="registro_cliente.php" class="btn_new">Crear cliente</a>
		<form action="buscar_cliente.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>

		<table>
			<tr>
				<th>ID</th>
				<th>C.I.</th>
				<th>Nombre</th>
				<th>Telefono</th>
				<th>Direccion</th>
				<th>Acciones</th>
			</tr>
			<?php 
				//CONTROL DE PAGINADOR
				$sql_registe = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM cliente WHERE estatus = 1");

				//almacenamos en la variable $result_register
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

				//consulta de usuarios activos
				$query = mysqli_query($conection, "SELECT * FROM cliente
															 WHERE estatus = 1 
															 ORDER BY idcliente ASC
															LIMIT $desde,$por_pagina 
															");

				mysqli_close($conection);//cierre de conexion

				$result = mysqli_num_rows($query);
				if($result > 0){
					while ($data = mysqli_fetch_array($query)) {
						if($data["nit"] == 0)
						{
							$nit = 'S/C';
						}else{
							$nit = $data['nit'];
						}

				?>	
					<tr>
						<td><?php echo $data['idcliente']; ?></td>
						<td><?php echo $nit; ?></td>
						<td><?php echo $data['nombre']; ?></td>
						<td><?php echo $data['telefono']; ?></td>
						<td><?php echo $data['direccion']; ?></td>
						<td>
							<!--capturamos la url por el idcliente -->
							<a class="link_edit" href="editar_cliente.php?id=<?php echo $data['idcliente']; ?>"><i class="fas fa-edit"></i> Editar</a>
							<?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) { //habilitamos las sesiones que tendran el privilegio de eliminar
							 ?>
							|
							<a class="link_delete" href="eliminar_confirmar_cliente.php?id=<?php echo $data['idcliente']; ?>"><i class="fas fa-trash-alt"></i> Eliminar</a>
							<?php } ?>
						</td>
					</tr>

				<?php 					
					}
				}
			 ?>		

		</table>

		<div class="paginador">
			<ul>

				<?php 
				//BLOQUEAR EL BOTON ULTIMA O 1RA PAGINA
				if ($pagina != 1) {

				 ?>
				<<li><a href="?pagina=<?php echo 1; ?>"><i class="fas fa-step-backward"></i></a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>"><i class="fas fa-caret-left fa-sm"></i></a></li>
				<?php 					
					}
				//PARA PINTAR EL CUADRO DE ENLACE
				for ($i=1; $i <=  $total_paginas; $i++) { 
					if ($i == $pagina) {
						echo '<li class ="pageSelected">'.$i.'</li>';
					}else{

					echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';

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
	</section>

	<?php include "includes/footer.php"; ?>

</body>
</html>