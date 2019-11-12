<?php 

include "../conexion.php";

	//si exsite post la accion va traer el valor
	if(!empty($_POST)){

		//extraer datos del producto
		if ($_POST['action'] == 'infoProducto') 
		{	
			$producto_id = $_POST['producto'];
			//ejecutamos la consulta
			$query = mysqli_query($conection,"SELECT codproducto,descripcion FROM producto
											WHERE codproducto = $producto_id AND estatus = 1");
			mysqli_close($conection);
			//obtenemos la consulta
			$result = mysqli_num_rows($query);
			//validacion
			if ($result > 0) {
				$data = mysqli_fetch_assoc($query);
				//retornamos en un formato json el array
				echo json_encode($data,JSON_UNESCAPED_UNICODE);//devuelve caracteres correctos
				exit;
			} 
			echo "error";

			exit;
		}

	}
	exit;
 ?>