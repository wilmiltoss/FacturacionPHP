<?php 
	session_start();//inicia el navegador
	include "../conexion.php";//detrocede un directorio

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Lista de Ventas</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">

		<h1><i class="fas fa-newspaper"></i> Lista de Ventas</h1>
		<a href="nueva_venta.php" class="btn_new"><i class="fas fa-plus"></i> Nueva venta</a>
		<!--BUSQUEDA POR NRO DE FACTURA-->
		<form action="buscar_venta.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Nro. Factura">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>

		<!--BUSQUEDA POR FECHA-->
		<div>
			<h5>Buscar por Fecha</h5>
			<form action="buscar_venta.php" method="get" class="form_search_date">
				<label>De: </label>
				<input type="date" name="fecha_de" id="fecha_de" required>
				<label> A </label>
				<input type="date" name="fecha_a" id="fecha_a" required>
				<button type="submit" class="btn_view"><i class="fas fa-search"></i></button>
			</form>
		</div>

		<table>
			<tr>
				<!--ENCABEZADOS-->
				<th>Nro.</th>
				<th>Fecha/hora</th>
				<th>Cliente</th>
				<th>Vendedor</th>
				<th>Estado</th>
				<th class="textrght">Total Factura</th><!--textrght el texto se alinea hacia la derecha-->
				<th class="textrght">Acciones</th>
			</tr>
			<?php 
				//CONTROL DE PAGINADOR
				$sql_registe = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM factura WHERE estatus != 10 ");//10 facturas eliminadas

				//almacenamos en la variable $result_register
				$result_register = mysqli_fetch_array($sql_registe);
				$total_registro = $result_register['total_registro'];

				//muestra la cantidade registros por pagina
				$por_pagina = 15; 

				if(empty($_GET['pagina']))
				{
					$pagina = 1;
				}else{
					$pagina = $_GET['pagina'];
				}

				$desde = ($pagina-1) * $por_pagina;
				$total_paginas = ceil($total_registro / $por_pagina); //ceil redondea los nros.

				//Extraemos toda la informacion de la factura, vendedor, cliente.
				$query = mysqli_query($conection, "SELECT f.nofactura,f.fecha,f.totalfactura,f.codcliente,f.estatus,u.nombre as vendedor,
																													cl.nombre as cliente
													 FROM factura f
													 INNER JOIN usuario u ON f.usuario = u.idusuario
													 INNER JOIN cliente cl ON f.codcliente = cl.idcliente
													 WHERE f.estatus != 10 
													 ORDER BY f.fecha DESC LIMIT $desde,$por_pagina 
															");

				mysqli_close($conection);//cierre de conexion

				//Almacenamos el query ejecutado
				$result = mysqli_num_rows($query);
				if($result > 0){//si hay registros
					//ARMAMOS EL LISTADO DE FACTURA VENTAS
					while ($data = mysqli_fetch_array($query)) {//$data es el que contiene todo el array, los datos, extraidos del query
						//VALIDAMOS EL ESTADO DE LA FACTURA AF.1-
						if ($data['estatus'] == 1) {
							$estado = '<span class="pagada">Pagada</span>';
						}else{
							$estado = '<span class="anulada">Anulada</span>';
						}
	
				?>	<!--FILAS QUE SE VA ARMANDO-->
					<tr id="row_<?php echo $data["nofactura"]; ?>"><!--agregamos un id con el nro de factura= sirve para las acciones a realizar-->
						<td><?php echo $data['nofactura']; ?></td>
						<td><?php echo $data['fecha']; ?></td>
						<td><?php echo $data['cliente']; ?></td>
						<td><?php echo $data['vendedor']; ?></td>
						<td class="estado"><?php echo $estado; ?></td><!--Variable de validacion AF.1 y la clase estado AI.1-->
						<td class="textrght totalfactura"><span>Gs.</span><?php echo $data["totalfactura"]; ?></td><!--$data["totalfactura"] extraemos del array-->
						<td>
								<!--LAS ACCIONES -->
							<div class="div_acciones">
								<div>
									<!--estiramos los datos dentro del cl y f -->
									<button class="btn_view view_factura" type="button" cl="<?php echo $data["codcliente"]; ?>" f="<?php echo $data['nofactura']; ?>"><i class="fas fa-eye"></i></button>
								</div>	
							
							<!--validacion para que solo puedan anular los administradores y deshabilitar  del boton anular si ya esta anulado -->
							<?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
									if ($data["estatus"] == 1) //validacion de pagado
									{		
							 ?>
										<div class="div_factura">
											<button class="btn_anular anular_factura" fac="<?php echo $data["nofactura"]; ?>"><i class="fas fa-ban"></i></button>
										</div>
						   <?php 
								    }else{ ?>
									    <div class="div_factura">
									    	<button type="button" class="btn_anular inactive"><i class="fas fa-ban"></i></button>
										</div>
						<?php } 
						}
						?>	
							</div>	
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