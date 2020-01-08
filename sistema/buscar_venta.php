<?php 
	session_start();//inicia el navegador
	include "../conexion.php";//detrocede un directorio

	//variables
	$busqueda = '';
	$fecha_de = '';
	$fecha_a  = '';

	//validacion de la url lupa
	if ( isset($_REQUEST['busqueda']) && $_REQUEST['busqueda'] =='')//con isset vemos si existe en la url esta variable
	{
		header("location: ventas.php");
	}
	//validacion de la url fechas
	if ( isset($_REQUEST['fecha_de']) || isset($_REQUEST['fecha_a'])) //con isset vemos si existe en la url esta variable
	{
		if ( $_REQUEST['fecha_de'] == '' || $_REQUEST['fecha_a'] == '' ) 
		{
			header("location: ventas.php");
		}
	}


	//1) validar la busqueda por lupa
	if (!empty($_REQUEST['busqueda'])){//si la variable busqueda no esta vacio(lo q muestra en el navegador url html)
		if (!is_numeric($_REQUEST['busqueda'])) {//si no es nro lo q enviamos
			header("location: ventas.php");//vuelve a ventas.php
		}
		$busqueda = strtolower($_REQUEST['busqueda']);//guardamos en una variable la busqueda q viene de la url
		//para el query
		$where    = "nofactura = $busqueda";//idicamos que el nro de factura se igual a la variable $busqueda
		//para el paginador
		$buscar   = "busqueda = $busqueda";//busqueda debe ser igual la variable $busqueda
	}

	//2) validamos la busqueda por medio de la fecha
	if (!empty($_REQUEST['fecha_de']) && !empty($_REQUEST['fecha_a'])) {//si no viene vacio las variables
		//capturamos lo que viene de la url
		$fecha_de = $_REQUEST['fecha_de'];
		$fecha_a = $_REQUEST['fecha_a'];

		$buscar = '';
		//si fecha_de es mayor a fecha_a
		if ($fecha_de > $fecha_a) {
			header("location: ventas.php");//regresa a ventas.php(si la busqueda incorrecta)
		}else if($fecha_de == $fecha_a){//si ambas fechas son iguales
			$where = "fecha LIKE '$fecha_de%'";//utlizamos la variable $fecha_de
			$buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";//para el paginador

		}else{
			$f_de  = $fecha_de.' 00:00:00';//le concatenamos las horas para ser igual al formato del sql
			$f_a   = $fecha_a.' 23:59:59';
			$where = "fecha BETWEEN '$f_de' AND '$f_a'";//consulta concatenado
			$buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";//p/ los nros de la paginacion
		}
	}

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
		<!--FORMULARIO-->
		<form action="buscar_venta.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Nro. Factura" value="<?php echo $busqueda;//mantener los campos cargados ?>">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>

		<!--BUSQUEDA POR FECHA-->
		<div>
			<h5>Buscar por Fecha</h5>
			<form action="buscar_venta.php" method="get" class="form_search_date">
				<label>De: </label>
				<input type="date" name="fecha_de" id="fecha_de" value="<?php echo $fecha_de;//mantiene los campos cargados ?>" required>
				<label> A </label>
				<input type="date" name="fecha_a" id="fecha_a" value="<?php echo $fecha_a;//mantiene los campos cargados ?>"  required>
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
				$sql_registe = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM factura WHERE $where ");//utilizamos la variable $where

				//almacenamos en la variable $result_register
				$result_register = mysqli_fetch_array($sql_registe);
				$total_registro = $result_register['total_registro'];

				//muestra la cantidade registros por pagina
				$por_pagina = 5; 

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
													 WHERE $where AND f.estatus != 10 
													 ORDER BY f.fecha DESC LIMIT $desde,$por_pagina 
															");//utilizamos la variable $where p/ bifurcar la busqueda

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
				<<li><a href="?pagina=<?php echo 1; ?>&<?php echo $buscar;/*utilizamos la variable*/?>"><i class="fas fa-step-backward"></i></a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>&<?php echo $buscar;/*utilizamos la variable*/?>"><i class="fas fa-caret-left fa-sm"></i></a></li>
				<?php 					
					}
				//PARA PINTAR EL CUADRO DE ENLACE
				for ($i=1; $i <=  $total_paginas; $i++) { 
					if ($i == $pagina) {
						echo '<li class ="pageSelected">'.$i.'</li>';
					}else{

					echo '<li><a href="?pagina='.$i.'&'.$buscar.'">'.$i.'</a></li>';//concatenamos la variable .$buscar

					}
				}
				if ($pagina != $total_paginas) 
				{

				 ?>
				
			    <li><a href="?pagina=<?php echo $pagina + 1; ?>&<?php echo $buscar;/*utilizamos la variable*/?>"><i class="fas fa-caret-right fa-sm"></i></a></li>
				<li><a href="?pagina=<?php echo $total_paginas; ?>&<?php echo $buscar;/*utilizamos la variable*/?>"><i class="fas fa-step-forward "></i></a></li>
				<?php 
					}
				 ?>

			</ul>
			
		</div>
	</section>

	<?php include "includes/footer.php"; ?>

</body>
</html>