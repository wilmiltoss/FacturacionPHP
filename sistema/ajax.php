<?php 


include "../conexion.php";

	//print_r($_POST);exit; // imprime los valores en array
	//incializamos la session para guardar en las variables
	session_start();
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
				//retornamos los datos en un formato json del array
				echo json_encode($data,JSON_UNESCAPED_UNICODE);//omite los caracteres especiales
				exit; 
			} 
			echo "error";


			exit;
		}
		//AGREGAR PRODUCTOS A ENTRADA
		if ($_POST['action'] == 'addProduct')
		{
			//si los campos del array no estan vacios
			if(!empty($_POST['cantidad']) || !empty($_POST['precio']) || !empty($_POST['producto_id']))
			{
				//guardamos los datos en variables
				$cantidad = $_POST['cantidad'];
				$precio = $_POST['precio'];
				$producto_id = $_POST['producto_id'];
				$usuario_id = $_SESSION['idUser'];//capturamos en $usuario_id la session del usuario

				$query_insert = mysqli_query($conection,"INSERT INTO entradas(codproducto, cantidad, precio, usuario_id) 
															         VALUES ($producto_id,$cantidad,$precio,$usuario_id)");

				//Si el insert se inserto correctamente
				if($query_insert){
			 		//Ejecutamos el procedimiento almacenado 
			 		$query_upd = mysqli_query($conection,"CALL actualizar_precio_producto($cantidad,$precio,$producto_id)");
			 		//si devuelve una fila en la actualizacion, se ejecuto correctamente
			 		$result_pro = mysqli_num_rows($query_upd);
			 		//validamos la devolucion, si es mayor a 0
			 		if($result_pro > 0){
			 			//guaramos en una variable data
			 			$data = mysqli_fetch_assoc($query_upd);
			 			//podemos devolver el id del producto en el array $data, un items mas aparte de los dos antes enviados en $data
			 			$data['producto_id'] = $producto_id;
			 			//devolvemos resultado en formato json el array $data, entonces el json lleva 3 datos
			 			echo json_encode($data,JSON_UNESCAPED_UNICODE);//omite los caracteres especiales
						exit;
			 		}else{
			 			echo "error";//A.1
			 		}
			 		//cerramos conexion
			 		mysqli_close($conection);

				}else{

					echo "error";
				}
				exit;
			}

		}

	}
	exit;
 ?>