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
			$query = mysqli_query($conection,"SELECT codproducto,descripcion,existencia,precio FROM producto
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
 
		//eliminar producto
		if ($_POST['action'] == 'delProduct')
		{
			//validar lo que viene en el metodo post no este vacio o que no sea numero
			if (empty($_POST['producto_id']) || !is_numeric($_POST['producto_id'])){
					echo "error";
				}else{ 
					//de lo contrario se almacena el valor rescatado de la url en la variable $idproducto
					$idproducto = $_POST['producto_id'];
					$query_delete = mysqli_query($conection,"UPDATE producto SET estatus = 0 WHERE codproducto= $idproducto");
					//cierre de conexion
					mysqli_close($conection);
					//si se ejecuto correctamente $query_delete
					if($query_delete){
						echo "ok";
					}else{
						echo "error";		
					}
				}
				echo "error"; //L.1
				exit;
		}

		//Buscar Cliente S.1;
		if ($_POST['action'] == 'searchCliente') // si lo que viene del metodo POST de action de js es igual a searchCliente
		{
			//print_r($_POST); 
			//echo "buscar cliente";
			if (!empty($_POST['cliente'])){//que el array que viene del POST no este vacio
				$nit = $_POST['cliente'];//guardamos en la variable nit

				$query = mysqli_query($conection,"SELECT * FROM cliente WHERE nit LIKE '$nit' AND estatus = 1 ");
				mysqli_close($conection);
				$result = mysqli_num_rows($query);//obtenemos la cantidad de filas devueltas

				$data = '';
				if ($result > 0) {
					$data = mysqli_fetch_assoc($query);//extraemos la informacion del query y lo almacenamos en data
				}else{
					$data = 0;
				}
				echo json_encode($data,JSON_UNESCAPED_UNICODE);//encodeamos la respuesta y le sacamos los simbolos
			}
			exit;
		}	

		//Registrar Clientes - Ventas
		if ($_POST['action'] == 'addCliente') // si lo que viene del metodo POST de action de js es igual a addCliente U.1
		{
			//print_r($_POST);//Vemos los datos que enviamos
			//Extraemos los datos del array y guardamos en las variables
			$nit		=$_POST['ci_cliente'];
			$nombre		=$_POST['nom_cliente'];
			$telefono	=$_POST['tel_cliente'];
			$direccion	=$_POST['dir_cliente'];
			$usuario_id	=$_SESSION['idUser'];//variable de session

			$query_insert = mysqli_query($conection,"INSERT INTO cliente(nit,nombre,telefono,direccion,usuario_id)
															   VALUES ('$nit','$nombre','$telefono','$direccion','$usuario_id')");

			if ($query_insert) {//si es  verdadero
				$codCliente = mysqli_insert_id($conection);//extraemos el id del cliente
				$msg = $codCliente;//guardamos en la variable el id del cliente
			}else{
				$msg = 'error';
			}
			mysqli_close($conection);
			echo $msg;
			exit;
		}

		//Agregar producto al detalle temporal
		if ($_POST['action'] == 'addProductoDetalle'){  // si lo que viene del metodo POST de action de js es igual a addProductoDetalle W.1	
		   //ref Y.1
			if (empty($_POST['producto']) || empty($_POST['cantidad'])) //si los campos del array rescatado del js estan vacio
			{
				echo "error"; 
			}else{//de lo contrario almacenamos los datos en las variables
				$codproducto = $_POST['producto'];
				$cantidad	 = $_POST['cantidad'];
				$token		 = md5($_SESSION['idUser']);//encriptando el id del usuario

				//query de extraccion del IVA
				$quey_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
				$result_iva = mysqli_num_rows($quey_iva);//obtenemos la cantidad de filas devueltas

				//ejecutamos el procedimiento almacenado, cargados por las variables los parametros rescatados del array 
				$query_detalle_temp = mysqli_query($conection,"CALL add_detalle_temp($codproducto,$cantidad,'$token')");
				$result = mysqli_num_rows($query_detalle_temp);//devuelve la cantidad de filas del procedimiento almacenado

				//VARIABLES PARA CALCULOS
				$detalleTabla = '';
				$sub_total	  = 0;
				$iva 		  = 0;
				$total 		  = 0;
				$arrayData	  = array();//array vacio Y.1

				//validamos la variable $result
				if ($result > 0) { //si hay filas
					if ($result_iva > 0) { //si hay filas
						$info_iva = mysqli_fetch_assoc($quey_iva);//almacenamos en un array por medio de mysqli_fetch_assoc
						$iva 	  = $info_iva['iva'];//nos dirigimos a la posicion iva del array y lo almacenamos en la variable $iva
					}
					//despues de extraer el IVA hacemos correr un while para recorrer todos los registros que nos devolvio el proc almacenado
					while ($data = mysqli_fetch_assoc($query_detalle_temp)) {//en $data almacenamos todo el query q nos devuleve el pr en un array
						//antes calculamos ciertos datos para sacar los totales
						$precioTotal = round($data['cantidad'] * $data['precio_venta'],2);//calculamos el precio total, extraemos del array
						$sub_total 	 = round($sub_total + $precioTotal,2);//con round redondeamos y con 2 decimales
						$total 		 = round($total + $precioTotal,2);

						//traemos todo el html del detalle de la venta y la guardamos en $detalleTabla
						//.$data['codproducto']. ,.$data['descripcion']. etc..  obenemos desde el array guardado en $data 
						//.$precioTotal. extraemos de la variable del calculo de arriba $precioTotal
						//el punto(.) para concatenar
						$detalleTabla .= '<tr>
											<td>'.$data['codproducto'].'</td>
											<td colspan="2">'.$data['descripcion'].'</td>
											<td class="textcenter">'.$data['cantidad'].'</td>
											<td class="textright">'.$data['precio_venta'].'</td>
											<td class="textright">'.$precioTotal.'</td>
											<td class="">
												<a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i></a>
											</td>
										</tr>';//X.1
										//.$data['codproducto']. capturamos el parametro para eliminar
					}
					//CALCULAMOS LOS TOTALES TOTALES resumen
					 $impuesto	= round($sub_total * ($iva / 100), 2);//multiplicamos el subtotal por el porcentaje indicado
					 $tl_sniva	= round($sub_total - $impuesto, 2);//total sin iva, le restamos al subtotal el impuesto
					 $total 	= round($tl_sniva + $impuesto,2);//total general

					 //almacenamos los resultados de los totales generales en una variable
					 $detalleTotales = '<tr><!--Fila de los TOTALES-->
											<td colspan="5" class="textright">SUBTOTAL Q.</td>
											<td class="textright">'.$tl_sniva.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">IVA ('.$iva. '%)</td>
											<td class="textright">'.$impuesto.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">TOTAL Q.</td>
											<td class="textright">'.$total.'</td>
										</tr>';

				//INGRESAMOS LOS DATOS AL arrayData Y.1
					//le colocamos al arrayData las variables del html creado arriba
					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					//retornamos el array y convertimos en formato json es decir eliminamos los simbolos especiales
					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);

				}else{
					echo "error";

				}
				mysqli_close($conection);

			}
			exit;
			
		}

	     //Validamos el action serchForDetalle Z.1 de tener vigencia pantalla nueva venta
		//Extrae datos del detalle_temp
		if ($_POST['action'] == 'serchForDetalle'){  // Validamos lo que viene serchForDetalle no venga vacio
		   //ref Z .1
			if (empty($_POST['user'])) //si los campos del array rescatado del js estan vacio
			{
				echo "error"; 
			}else{//de lo contrario generamos el tockent del usuario p/ hacer la consulta
				$token		 = md5($_SESSION['idUser']);//encriptando el id del usuario

				$query = mysqli_query($conection, "SELECT tmp.correlativo,
														  tmp.token_user,
														  tmp.cantidad,
														  tmp.precio_venta,
														  p.codproducto,
														  p.descripcion
												    FROM detalle_temp tmp
												    INNER JOIN producto p 
												    ON tmp.codproducto = p.codproducto
												    WHERE token_user = '$token' ");
 
				$result = mysqli_num_rows($query);
				//query de extraccion del IVA 
				$quey_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
				$result_iva = mysqli_num_rows($quey_iva);//obtenemos la cantidad de filas devueltas

				//VARIABLES PARA CALCULOS
				$detalleTabla = '';
				$sub_total	  = 0;
				$iva 		  = 0;
				$total 		  = 0;
				$arrayData	  = array();//array vacio Y.1

				//validamos la variable $result
				if ($result > 0) { //si hay filas
					if ($result_iva > 0) { //si hay filas
						$info_iva = mysqli_fetch_assoc($quey_iva);//almacenamos en un array por medio de mysqli_fetch_assoc
						$iva 	  = $info_iva['iva'];//nos dirigimos a la posicion iva del array y lo almacenamos en la variable $iva
					}
					//despues de extraer el IVA hacemos correr un while para recorrer todos los registros que nos devolvio el $query almacenado
					while ($data = mysqli_fetch_assoc($query)) {//en $data almacenamos todo el query q nos devuleve la consulta  $query 
						//antes calculamos ciertos datos para sacar los totales
						$precioTotal = round($data['cantidad'] * $data['precio_venta'],2);//calculamos el precio total, extraemos del array
						$sub_total 	 = round($sub_total + $precioTotal,2);//con round redondeamos y con 2 decimales
						$total 		 = round($total + $precioTotal,2);

						//traemos todo el html del detalle de la venta y la guardamos en $detalleTabla
						//.$data['codproducto']. ,.$data['descripcion']. etc..  obenemos desde el array guardado en $data 
						//.$precioTotal. extraemos de la variable del calculo de arriba $precioTotal
						//el punto(.) para concatenar
						$detalleTabla .= '<tr>
											<td>'.$data['codproducto'].'</td>
											<td colspan="2">'.$data['descripcion'].'</td>
											<td class="textcenter">'.$data['cantidad'].'</td>
											<td class="textright">'.$data['precio_venta'].'</td>
											<td class="textright">'.$precioTotal.'</td>
											<td class="">
							 					<a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i></a>
											</td>
										</tr>';//X.1
										//.$data['codproducto']. capturamos el parametro para eliminar
					}
					//CALCULAMOS LOS TOTALES TOTALES resumen
					 $impuesto	= round($sub_total * ($iva / 100), 2);//multiplicamos el subtotal por el porcentaje indicado
					 $tl_sniva	= round($sub_total - $impuesto, 2);//total sin iva, le restamos al subtotal el impuesto
					 $total 	= round($tl_sniva + $impuesto,2);//total general

					 //almacenamos los resultados de los totales generales en una variable
					 $detalleTotales = '<tr><!--Fila de los TOTALES-->
											<td colspan="5" class="textright">SUBTOTAL Q.</td>
											<td class="textright">'.$tl_sniva.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">IVA ('.$iva. '%)</td>
											<td class="textright">'.$impuesto.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">TOTAL Q.</td>
											<td class="textright">'.$total.'</td>
										</tr>';

				//INGRESAMOS LOS DATOS AL arrayData Y.1
					//le colocamos al arrayData las variables del html creado arriba
					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					//retornamos el array y convertimos en formato json es decir eliminamos los simbolos especiales
					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);

				}else{
					echo "error";

				}
				mysqli_close($conection);

			}
			exit;	
		}


		// AA.1 Validamos del_product_detalle si esta enviando algun parametro de del_product_detalle
		if ($_POST['action'] == 'del_product_detalle'){  // Validamos lo que viene del_product_detalle no venga vacio
			//print_r($_POST);exit;//mostramos en consola como array
		   //ref AA .1
			if (empty($_POST['id_detalle'])) //si los campos del array rescatado del js estan vacio
			{
				echo "error"; 
			}else{//de lo contrario generamos el tockent del usuario p/ hacer la consulta
				$id_detalle  = $_POST['id_detalle'];
				$token		 = md5($_SESSION['idUser']);//encriptando el id del usuario

				//query de extraccion del IVA 
				$quey_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
				$result_iva = mysqli_num_rows($quey_iva);//obtenemos la cantidad de filas devueltas

				// ejecutamos el procedimiento almacenado
				$query_detalle_temp = mysqli_query($conection,"CALL del_detalle_temp($id_detalle,'$token')");
				$result = mysqli_num_rows($query_detalle_temp);//obtenemos el resultado del nro de filas

				//VARIABLES PARA CALCULOS
				$detalleTabla = '';
				$sub_total	  = 0;
				$iva 		  = 0;
				$total 		  = 0;
				$arrayData	  = array();//array vacio Y.1

				//validamos la variable $result
				if ($result > 0) { //si hay filas
					if ($result_iva > 0) { //si hay filas
						$info_iva = mysqli_fetch_assoc($quey_iva);//almacenamos en un array por medio de mysqli_fetch_assoc
						$iva 	  = $info_iva['iva'];//nos dirigimos a la posicion iva del array y lo almacenamos en la variable $iva
					}
					//despues de extraer el IVA hacemos correr un while para recorrer todos los registros que nos devolvio el $query almacenado
					while ($data = mysqli_fetch_assoc($query_detalle_temp)) {//en $data almacenamos todo el query q nos devuleve la consulta  $query 
						//antes calculamos ciertos datos para sacar los totales
						$precioTotal = round($data['cantidad'] * $data['precio_venta'],2);//calculamos el precio total, extraemos del array
						$sub_total 	 = round($sub_total + $precioTotal,2);//con round redondeamos y con 2 decimales
						$total 		 = round($total + $precioTotal,2);

						//traemos todo el html del detalle de la venta y la guardamos en $detalleTabla
						//.$data['codproducto']. ,.$data['descripcion']. etc..  obenemos desde el array guardado en $data 
						//.$precioTotal. extraemos de la variable del calculo de arriba $precioTotal
						//el punto(.) para concatenar
						$detalleTabla .= '<tr>
											<td>'.$data['codproducto'].'</td>
											<td colspan="2">'.$data['descripcion'].'</td>
											<td class="textcenter">'.$data['cantidad'].'</td>
											<td class="textright">'.$data['precio_venta'].'</td>
											<td class="textright">'.$precioTotal.'</td>
											<td class="">
							 					<a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i></a>
											</td>
										</tr>';//X.1
										//.$data['codproducto']. capturamos el parametro para eliminar
					}
					//CALCULAMOS LOS TOTALES TOTALES resumen
					 $impuesto	= round($sub_total * ($iva / 100), 2);//multiplicamos el subtotal por el porcentaje indicado
					 $tl_sniva	= round($sub_total - $impuesto, 2);//total sin iva, le restamos al subtotal el impuesto
					 $total 	= round($tl_sniva + $impuesto,2);//total general

					 //almacenamos los resultados de los totales generales en una variable
					 $detalleTotales = '<tr><!--Fila de los TOTALES-->
											<td colspan="5" class="textright">SUBTOTAL Q.</td>
											<td class="textright">'.$tl_sniva.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">IVA ('.$iva. '%)</td>
											<td class="textright">'.$impuesto.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">TOTAL Q.</td>
											<td class="textright">'.$total.'</td>
										</tr>';

				//INGRESAMOS LOS DATOS AL arrayData Y.1
					//le colocamos al arrayData las variables del html creado arriba
					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					//retornamos el array y convertimos en formato json es decir eliminamos los simbolos especiales
					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);

				}else{
					echo "error";

				}
				mysqli_close($conection);

			}
			exit;
		}

		//Anular Venta
		if ($_POST['action'] == 'anularVenta') // si lo que viene del metodo POST de action de js es igual a anularVenta AC.1
		{
	
			$token	=md5($_SESSION['idUser']);//generamos un token c on variable de session
			//eliminamos de la tabla detalle_temp segun el token
			$query_del = mysqli_query($conection,"DELETE FROM detalle_temp WHERE token_user = '$token' ");
			mysqli_close($conection);
			if ($query_del) {
				echo "ok";
			}else{
				echo "error";
			}
			exit;
		}

		//Procesar Venta
		if ($_POST['action'] == 'procesarVenta'){ // si lo que viene del metodo POST de action de js es igual a anularVenta AD.1
		
			//print_r($_POST);exit;

			if (empty($_POST['codcliente'])){ //si el codcliente viene vacio de
				$codcliente = 1;//entonces la variable va ser igual a 1, le asignamos el registro 1 de la tabla cliente

			} else{
				$codcliente = $_POST['codcliente'];//de lo contrario tomamos la variabl que trae el codcliente
			}
			//Generamos el token por medio de la variable de sesion
			$token 		= md5($_SESSION['idUser']); //Guardamos la session del usuario q genera la venta encriptado
			$usuario 	= $_SESSION['idUser']; //Guardamos la sesion del usuario q genera la venta - sin encriptar


			//Verificamos si existe producto en la tabla detalle_temp, si el vendedor esta generando algna venta
			$query = mysqli_query($conection,"SELECT * FROM detalle_temp WHERE token_user = '$token' ");
			$result = mysqli_num_rows($query);

			if ($result > 0) {//si existe registro ejecutamos el procedimiento almacenado generar venta
				//los parametros se cargan con las variables de arriba

				$query_procesar = mysqli_query($conection,"CALL procesar_venta($usuario,$codcliente,'$token')");
				$result_detalle = mysqli_num_rows($query_procesar);//cuenta los nros de registros de la fila
				

				if ($result_detalle > 0) {
					$data = mysqli_fetch_assoc($query_procesar);//guardamos en $data el array
					echo json_encode($data, JSON_UNESCAPED_UNICODE);//convertimos en formato json el resultado
				}else{
					echo "error";
					
					}
			}else{
				echo "error"; 
			}
			mysqli_close($conection);
			exit;
		}  

		//Info Factura p/ la ventana ajax de anulacion  
		if ($_POST['action'] == 'infoFactura'){ // si lo que viene del metodo POST de action de js es igual a infoFactura
			//print_r($_POST);exit;
			if (!empty($_POST['nofactura'])){ //si el noFactura no viene vacio del js AG.1

				$nofactura = $_POST['nofactura'];
				$query = mysqli_query($conection,"SELECT * FROM factura WHERE nofactura = '$nofactura' AND estatus = 1");
				mysqli_close($conection);

				$result = mysqli_num_rows($query);//almacenamos el query en resul

				if ($result > 0) {
					$data = mysqli_fetch_assoc($query);//guardamos en $data el array
					echo json_encode($data, JSON_UNESCAPED_UNICODE);//convertimos en formato json el resultado
					exit;	
					}
				}
				echo "error";
				exit;
		}  


		//Anular Factura 
		if ($_POST['action'] == 'anularFactura'){ // si lo que viene del metodo POST de action de js es igual a anularFactura AH.1

			if (!empty($_POST['noFactura']))//si el noFactura no viene vacio del js 
			{ 
				$noFactura = $_POST['noFactura'];
				//llamamos al procedimiento anular_factura
				$query_anular = mysqli_query($conection,"CALL anular_factura($noFactura)");
				mysqli_close($conection);
				$result = mysqli_num_rows($query_anular);//almacenamos el query en resul

				if ($result > 0) {
					$data = mysqli_fetch_assoc($query_anular);//guardamos en $data el array
					echo json_encode($data, JSON_UNESCAPED_UNICODE);//convertimos en formato json el resultado
					exit;	
					}
				}
				echo "error";
				exit;
		}  


		//Boton cambiar contraseña
		if ($_POST['action'] == 'changePassword'){ // si lo que viene del metodo POST de action de js es igual a changePassword AG.1
			//print_r($_POST);//mostrar en consola

			if (!empty($_POST['passActual']) && !empty($_POST['passNuevo'])) //si las respuestas post no vienen vacios AJ.1
			{
				//almacenamos en variables las recepciones del post
				$password = md5($_POST['passActual']);//encriptamos
				$newPass  = md5($_POST['passNuevo']);//encriptamos
				$idUser	  = $_SESSION['idUser'];

				//otras variables
				$cod 	  = ''; //inicializamos vacio
				$msg 	  = ''; //inicializamos vacio
				$arrData  = array(); //inicializamos vacio el array 

			    $query_user = mysqli_query($conection, "SELECT * FROM usuario
													    WHERE clave = '$password' AND idusuario = $idUser ");


				$result    = mysqli_num_rows($query_user);

				//actualizamos la contraseña si $result es mayor a 0
				if ($result > 0) 
				{
					$query_update = mysqli_query($conection, "UPDATE usuario SET clave = '$newPass' WHERE idusuario = $idUser ");
					mysqli_close($conection);

					//validamos la actualizacion, si lo realizo correctamente
					if ($query_update){//si devuelve verdadera
						$code ='00';//coloca a la variable 00
						$msg  = "Su contraseña se ha actualizado con exito.";
					}else{
						$code = '2';//coloca a la variable 2
						$msg  = "No es posible cambiar su contraseña.";

					}
				}else{//si no encuentra la consulta
					$code = '1';//coloca a la variable 1
					$msg  = "La contraseña actual es incorrecta.";
				}
				//enviamos el array
				$arrData = array('cod' => $code, 'msg' => $msg);//le colocamos las variable $cod y $msg
				//convertimos el array en formato json
				echo json_encode($arrData, JSON_UNESCAPED_UNICODE);//AJ.1

			}else{
				echo "error"; //AJ.1
			}
			
		 exit;
		} 

		//Actualizar datos de la empresa AK.1
		if ($_POST['action'] == 'updateDataEmpresa') {//updateDataEmpresa viene del html
			//print_r($_POST);exit; prueba mostrar datos
			//validamos que los campos no esten vacios
			if (empty($_POST['txtCI']) || empty($_POST['txtNombre']) || empty($_POST['txtTelEmpresa']) || empty($_POST['txtEmailEmpresa']) || empty($_POST['txtDirEmpresa']) || empty($_POST['txtIva'])  ) 
			{
				$code = '1';
				$msg  = "Todos los campos son obligatorios";
			}else{ //de lo contrario

				$intCI     = intval($_POST['txtCI']);//obtenemos un valor entero con intval
				$strNombre = $_POST['txtNombre'];
				$strRSocial= $_POST['txtRSocial'];
				$intTel	   = intval($_POST['txtTelEmpresa']);
				$strEmail  = $_POST['txtEmailEmpresa'];
				$strDir    = $_POST['txtDirEmpresa'];
				$strIva    = $_POST['txtIva'];

				//actualizamos segun los campos cargados
				$queryUpd = mysqli_query($conection, "UPDATE configuracion SET ci    = $intCI,
																		nombre       = '$strNombre',
																		razon_social = '$strRSocial',
																		telefono     = $intTel,
																		email        = '$strEmail',
																		direccion    = '$strDir',
																		iva          = $strIva
																	WHERE id = 1 ");
				mysqli_close($conection);
				//evaluamos si se ejecuta
				if ($queryUpd) {
					$code = '00';//colocamos a la variable $code 00
					$msg  = "Datos actualizados correctamente.";//con el mensaje q enviamos al function Ak.2
				}else{ //de lo contrario
					$code = '2';//le colocamos un 2
					$msg  = "Error al actualizar los datos.";//con el mensaje q enviamos al function Ak.2
				}
			}
			//asignamos  a la variable $arrData el elemento cod y msg 
			$arrData = array('cod' => $code, 'msg' => $msg);
			echo json_encode($arrData, JSON_UNESCAPED_UNICODE);//convertimos en json y lo enviamos sin caracteres
			exit;
	
		 } 

	}
	exit;

 ?>