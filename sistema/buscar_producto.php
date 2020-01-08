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
		<?php 
			$busqueda= '';
			$search_proveedor= '';
			//si esta vacio la variable busqueda y proveedor
			if (empty($_REQUEST['busqueda']) && empty($_REQUEST['proveedor'])) 
			{	//regresa a listado_producto
				header("location: lista_producto.php");
			}
			//si no esta vacio busqueda asignamos a la variable en minus cula
			if (!empty($_REQUEST['busqueda'])) {
				$busqueda = strtolower($_REQUEST['busqueda']);
				$where = "(p.codproducto LIKE '%$busqueda%' OR p.descripcion LIKE '%$busqueda%') AND p.estatus = 1   ";//LL.1
				//variable para la paginacion de la busqueda
				$buscar = 'busqueda='.$busqueda;//variable que enviamos P.1
			}
			//si no esta vacio proveedor colocamos esta variable en la url
			if (!empty($_REQUEST['proveedor'])) {
				$search_proveedor  = $_REQUEST['proveedor'];
				$where = "p.proveedor LIKE $search_proveedor AND p.estatus = 1 ";//LL.1
				//variable para la paginacion de la busqueda
				$buscar = 'proveedor='.$search_proveedor;//variable que enviamos P.1
			}
		 ?>

		<h1><i class="fas fa-cube"></i> Lista de Productos</h1>
		<a href="registro_producto.php" class="btn_new"> <i class="fas fa-plus"></i> Registrar producto</a>
		<form action="buscar_producto.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda; ?>"><!--O.1-->
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
 
		<table> 
			<tr>
				<th>Código</th>
				<th>Descripción</th>
				<th>Precio</th>
				<th>Existencia</th>
				<th>
				<?php 
					//validacion para que muestre en la casilla desplegable cabecera el proveedor seleccionado
					$pro = 0;
					//si no viene vacio la variable proveedor le asignamos a $pro el proveedor N.1
					if (!empty($_REQUEST['proveedor'])) {
						$pro = $_REQUEST['proveedor'];
					}
					//consulta
 					$query_proveedor = mysqli_query($conection, "SELECT codproveedor, proveedor FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC");
 					$result_proveedor = mysqli_num_rows($query_proveedor);
 				 ?>
 				 <!-- LISTA DESPLEGABLE DEL PROVEEDOR DE LA CABECERA-->
 				<select name="proveedor" id="search_proveedor"><!--LL.1-->
 					<option value="" selected>PROVEEDOR</option><!--O .1-->
 					<?php 
 						if ($result_proveedor > 0) {
 
 							while ($proveedor = mysqli_fetch_array($query_proveedor)) { 
 								//si la variable $pro muestra un dato 
 								if ($pro == $proveedor['codproveedor']) 
 								{								
 						?>  <!-- entonces arma el option en la lista desplegable con el atributo selected, seleciona con selected N.1-->
 							<option value="<?php echo $proveedor['codproveedor']; ?>" selected><?php echo $proveedor['proveedor']; ?></option>
 						<?php 
 								}else{
 						?>	  <!-- de lo contrario arma otro option sin el atributo selected-->
 							<option value="<?php echo $proveedor['codproveedor']; ?>"><?php echo $proveedor['proveedor']; ?></option>
 						<?php			
 								}
 							}
 						}
 					?>	
 				</select>


				</th>
				<th>Foto</th>
				<th>Acciones</th>
			</tr>

			<?php 
				//CONTROL DE PAGINADOR = filtra la opcion seleccionada 		
				$sql_registe = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM producto as p
																	   WHERE $where"); //LL.1
				//almacenamos en la variable $result_register
				$result_register = mysqli_fetch_array($sql_registe);
				$total_registro = $result_register['total_registro'];

				echo $total_registro;
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

				//consulta de proveedores
				$query = mysqli_query($conection, "SELECT p.codproducto, p.descripcion, p.precio, p.existencia, pr.proveedor, p.foto
													FROM producto p INNER join proveedor pr ON p.proveedor = pr.codproveedor
													 WHERE $where
													 ORDER BY p.codproducto DESC LIMIT $desde,$por_pagina");

				mysqli_close($conection);//cierre de conexion

				$result = mysqli_num_rows($query);
				if($result > 0){
					while ($data = mysqli_fetch_array($query)) {
						if ($data['foto'] != 'img_producto.png') {
							$foto = 'img/uploads/'.$data['foto'];//concateno el nombre
						}else{
							$foto = 'img/'.$data['foto'];
						}

				?>	
				<!-- ACTUALIZAMOS LA PLANILLA INMEDIATAMENTE DESPUES DE REALIZAR UN CAMBIO -->
				<!-- row = Clase q identifica a c/u de los productos -->
					<tr class="row<?php echo $data["codproducto"]; ?>">
						<td><?php echo $data['codproducto']; ?></td>
						<td><?php echo $data['descripcion']; ?></td>
						<td class="celPrecio"><?php echo $data['precio']; ?></td><!--C.1 -->
						<td class="celExistencia"><?php echo $data['existencia']; ?></td><!--C.2 -->
						<td><?php echo $data['proveedor']; ?></td>
						<td class="img_producto"> <img src="<?php echo $foto; ?>" alt="<?php echo $data['descripcion']; ?>"></td>
					
					<?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) { //habilitamos las sesiones que tendran el privilegio de eliminar
							 ?>
						<td>
							<a class="link_add add_product" product = "<?php echo $data["codproducto"]; ?>" href="#"><i class="fas fa-plus"></i> Agregar</a>
							|
							<!--capturamos la url por el idcliente -->
							<a class="link_edit" href="editar_producto.php?id=<?php echo $data["codproducto"]; ?>"><i class="fas fa-edit"></i> Editar</a>
							|	<!--utilizamos la ventana de ajax para el boton eliminar J.1 -->
							<a class="link_delete del_product" href="#" product = "<?php echo $data["codproducto"]; ?>"><i class="fas fa-trash-alt"></i> Eliminar</a>
						</td>
						<?php } ?>
					</tr>
				<?php 					
					}
				}
			 ?>		
		</table>
		<!--evitar que aparesca el boton paginador en caso de que no haya resultados en la busqueda-->
		<?php 
			if ($total_paginas != 0) 
			 {
		 ?>

		<div class="paginador">
			<ul>

				<?php 
				//BLOQUEAR EL BOTON ULTIMA O 1RA PAGINA
				if ($pagina != 1) {

				 ?>
				 <!--$buscar = variable para paginar solo lo seleccionado en la busqueda  P.1-->
				<<li><a href="?pagina=<?php echo 1; ?>&<?php echo $buscar; ?>"><i class="fas fa-step-backward"></i></a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>&<?php echo $buscar; ?>"><i class="fas fa-caret-left fa-sm"></i></a></li>
				<?php 					
					}
				//PARA PINTAR EL CUADRO DE ENLACE
				for ($i=1; $i <=  $total_paginas; $i++) {  
					if ($i == $pagina) {
						echo '<li class ="pageSelected">'.$i.'</li>';
					}else{

					echo '<li><a href="?pagina='.$i.'&'.$buscar.'">'.$i.'</a></li>';//concatenamos con $buscar. el nro de pagina P.1

					}
				}
				if ($pagina != $total_paginas) 
				{

				 ?>
				<!--$buscar = variable para paginar solo lo seleccionado en la busqueda  P.1-->
			    <li><a href="?pagina=<?php echo $pagina + 1; ?>&<?php echo $buscar; ?>"><i class="fas fa-caret-right fa-sm"></i></a></li>
				<li><a href="?pagina=<?php echo $total_paginas; ?>&<?php echo $buscar; ?>"><i class="fas fa-step-forward "></i></a></li>
				<?php 
					}
				 ?>

			</ul>	
		</div>
		<?php 

		}

		 ?>

	</section>

	<?php include "includes/footer.php"; ?>

</body>
</html>