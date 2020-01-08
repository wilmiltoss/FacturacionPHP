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
		if(empty($_POST['proveedor']) || empty($_POST['producto']) || empty($_POST['precio']) || $_POST['id'] <= 0 || empty($_POST['foto_actual']) || empty($_POST['foto_remove']))
		{
			$alert = '<p class="msg_error"> Todos los campos son obligatorios.</p>';
		}else{
			//variables de almacenamientos
			$codproducto = $_POST['id'];//I.1
			$proveedor   = $_POST['proveedor'];
			$producto    = $_POST['producto'];
			$precio      = $_POST['precio'];
			$imgProducto = $_POST['foto_actual'];
			$imgRemove   = $_POST['foto_remove'];

			$foto 		 = $_FILES['foto'];
			$nombre_foto = $foto['name'];
			$type		 = $foto['type'];
			$url_temp	 = $foto['tmp_name'];
 
			$upd = '';

			//CREAMOS EL NOMBRE DE LA FOTO
			// si el cuadro es dinstinto a vacio
			if ($nombre_foto != ''){
			 //guardamos la foto con el nombre de la fecha hora para no duplicarlos en la carpeta img/uploads
				$destino     = 'img/uploads/';
				$img_nombre  = 'img_'.md5(date('d-m-Y H:m:s')); //encriptamos la fecha p/ genrar el nombre aleatorio
				$imgProducto = $img_nombre.'.jpg'; //concatenamos la extencion del archivo
				$src 		 = $destino.$imgProducto; //guardamos el nombre en la variable concatenado en el destino
			}else{
				//si la foto actual es distinto a la foto remove quiere decir que se elimino la imagen
				if ($_POST['foto_actual'] != $_POST['foto_remove']) {
					$imgProducto = 'img_producto.png';// le colocamos a la variable el nombre
				}

			}

		 	
			$query_update = mysqli_query($conection,"UPDATE producto
													 SET descripcion = '$producto',
													 	 proveedor   = $proveedor,
													 	 precio      = $precio,
													 	 foto        = '$imgProducto'
														 WHERE codproducto = $codproducto"); //I.1
				//validacion de actualizacion
				if($query_update){
					//validamos si la variable foto esta vacio entonces hay una imagen adjunta nueva
					if (($nombre_foto != '' && ($_POST['foto_actual'] != 'img_producto.png')) || ($_POST['foto_actual'] != $_POST['foto_remove']))
					 {
						//entonces elimina con la funcion unlink la foto que se encuentra actualmente en la bd
						unlink('img/uploads/'.$_POST['foto_actual']);
					}
					// o guardamos normalmente la foto
					if ($nombre_foto != '') {
						//almacena en la ruta temporal y la mueve en el nuevo destino $src
						move_uploaded_file($url_temp, $src); 
					}
					$alert='<p class="msg_save">Producto actualizado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al actualizar producto.</p>';
				}
		}		
	}
 
	//VALIDAR PRODUCTO, que la variable no este vacia, barra de navegacion.
	if (empty($_REQUEST['id'])) { //si esta vacio
		header("location: lista_producto.php");//enviar aca
	}else{
		$id_producto = $_REQUEST['id'];//de lo contrario guardamos en la variable $id_producto
		//validar que el valor sea un nro
		if (!is_numeric($id_producto)) {//si no es nro.
			header("location: lista_producto.php");//enviar aca
		}
		//comprobar que la variable cargada en el navegador exista =xxx
		$query_producto = mysqli_query($conection,"SELECT p.codproducto,p.descripcion,p.precio,p.foto,pr.codproveedor,pr.proveedor
													 FROM producto p inner join proveedor pr ON p.proveedor = pr.codproveedor
													WHERE p.codproducto = $id_producto and p.estatus = 1");//D.1
		//contamos la cantidad de filas que devuelve el query
		$result_producto = mysqli_num_rows($query_producto);

		//variables para mostrar la foto en la tx E.1
		$foto = '';
		$classRemove = 'notBlock';//lo extraemos de style.css 
		//validamos si la variable $result_producto es mayor a 0
		if ($result_producto > 0) {
			//si es positvo lo almacenamos en la variable $data_producto 
			$data_producto = mysqli_fetch_assoc($query_producto);
			//validamos mostrar foto por defecto, si el producto no tiene foto
			if ($data_producto['foto'] != 'img_producto.png') {
				$classRemove = '';//cambiamos los valores a la variables vacias
				//cambiamos a la estructura de la imagen con su ruta concatenado a la variable $data_producto
				$foto = '<img id="img" src="img/uploads/'.$data_producto['foto'].'" alt="Producto">';
			}

			//imprimimos la variable: lo imprime en un array
			//print_r($data_producto);
			//sino redireccionamos a lista_productos.php
		}else{
			header("location: lista_producto.php");//enviar aca
		}
	}
 ?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
 	<meta charset="utf-8">
 	<?php include "includes/scripts.php"; ?>
 	<title>Actualizar Producto</title> 
 </head>
 <body>
 	<?php include "includes/header.php"; ?>
 	<section id="container">

 		<div class="form_register">
 			<h1><i class="fas fa-cubes"></i>  Actualizar producto</h1>
 			<hr>
 			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
 			<!-- multipart utilizado p/ adjuntar archivos-->
 			<form action="" method="post" enctype="multipart/form-data">
 				<!-- Editar los datos del producto G.1-->
 				<input type="hidden" name="id" value="<?php echo $data_producto['codproducto']; ?>">
 				<input type="hidden" id="foto_actual" name="foto_actual" value="<?php echo $data_producto['foto']; ?>">
 				<input type="hidden" id="foto_remove" name="foto_remove" value="<?php echo $data_producto['foto']; ?>">
 				<label for="proveedor">Proveedor</label>
 				<?php 
 					$query_proveedor = mysqli_query($conection, "SELECT codproveedor, proveedor FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC");
 					$result_proveedor = mysqli_num_rows($query_proveedor);
 					mysqli_close($conection);
 				 ?>
 				 <!-- LISTA DESPLEGABLE DEL PROVEEDOR-->
 				<select name="proveedor" id="proveedor" class="notItemOne">  <!-- Con class="notItemOne" oculta duplicaciones en la lista desplegable-->
 					<!-- mostrar en el txt los datos rescatados del producto con option extraidos del array que muestra $data_producto-->
 					<option value="<?php echo $data_producto['codproveedor'] ?>" selected><?php echo $data_producto['proveedor'] ?></option>
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
 				<!-- MOSTRAMOS CON value="<?php  $data_producto ?> los datos en la txt  D.1 -->
 				<input type="text" name="producto" id="producto" placeholder="Nombre completo del producto" value="<?php echo $data_producto['descripcion'] ?>">
 				<label for="precio">Precio</label>
 				<input type="number" name="precio" id="precio" placeholder="Precio del producto" value="<?php echo $data_producto['precio'] ?>">
 				<!-- carga las fotos  -->
				<div class="photo">
					<label for="foto">Foto</label>
				        <div class="prevPhoto">
				        <!-- mostramos la variable $classRemove de remocion delPhoto=E.1  -->
				        <span class="delPhoto <?php echo $classRemove; ?>">X</span>
				        <label for="foto"></label>
				        <?php //mostramos la variable $foto E.1
				        echo $foto; ?>
				        </div>
				        <div class="upimg">
				        <input type="file" name="foto" id="foto">
				        </div>
				        <div id="form_alert"></div>
				</div>
 				 <button type="submit" class="btn_save"><i class="fas fa-save"></i> Actualizar Producto</button>
 			</form>			

 		</div> 		

 	</section>
 	<?php include "includes/footer.php"; ?>
 </body>
 </html>