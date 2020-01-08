<?php

	//print_r($_REQUEST);
	//exit;
	//echo base64_encode('2');
	//exit;
	session_start();//validamos una sesion activa, si no existe, retorna a la carpeta raiz
	if(empty($_SESSION['active']))
	{
		header('location: ../');
	}

	include "../../conexion.php";//retrocedemos dos niveles
	require_once '../pdf/vendor/autoload.php';//incluimos la libreria, retrocedemos un nivel
	use Dompdf\Dompdf; //toma todos los demas archivos de las otras carpetas p/ generar el pdf

	if(empty($_REQUEST['cl']) || empty($_REQUEST['f']))//evaluamos que las variables tengan informacion de AD.1 
	{
		echo "No es posible generar la factura.";
	}else{
		//guardamos en  variables los valores
		$codCliente = $_REQUEST['cl'];
		$noFactura = $_REQUEST['f'];
		$anulada = '';
		//obtenemos los datos de la tabla configuracion AE.1 que lo enviamos a factura.php
		$query_config   = mysqli_query($conection,"SELECT * FROM configuracion");
		$result_config  = mysqli_num_rows($query_config);//contamos la  cantidad de filas
		if($result_config > 0){
			$configuracion = mysqli_fetch_assoc($query_config);
		}

		//DATOS DE LA FACTURA AF.1 -- DATE_FORMAT damos formato a la fecha y la hora 
		$query = mysqli_query($conection,"SELECT f.nofactura, DATE_FORMAT(f.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(f.fecha,'%H:%i:%s') as  hora, f.codcliente, f.estatus,
												 v.nombre as vendedor,
												 cl.nit, cl.nombre, cl.telefono,cl.direccion
											FROM factura f
											INNER JOIN usuario v
											ON f.usuario = v.idusuario
											INNER JOIN cliente cl
											ON f.codcliente = cl.idcliente
											WHERE f.nofactura = $noFactura AND f.codcliente = $codCliente  AND f.estatus != 10 ");//el codcliente y factura debe coincidir con las variables creadas mas arriba

		$result = mysqli_num_rows($query);//alacenamos el query
		if($result > 0){//si existe datos

			$factura = mysqli_fetch_assoc($query);//alacenamos en la variable $factura el resultado AF.1
			$no_factura = $factura['nofactura'];//accedemos al nofactura del array

			if($factura['estatus'] == 2){//si el estado de la factura es 2 entones esta anualda
				$anulada = '<img class="anulada" src="img/anulado.png" alt="Anulada">';//le colocamos una imagen, con su ubicacion url
			}
			//OBTENEMOS LOS DATOS DEL DETALLE
			$query_productos = mysqli_query($conection,"SELECT p.codproducto,p.descripcion,dt.cantidad,dt.precio_venta,(dt.cantidad * dt.precio_venta) as precio_total
														FROM factura f
														INNER JOIN detallefactura dt
														ON f.nofactura = dt.nofactura
														INNER JOIN producto p
														ON dt.codproducto = p.codproducto
														WHERE f.nofactura = $no_factura ");//segun el nro de factura

			//almacenamos en $result_detalle el query ejecutado AG.1
			$result_detalle = mysqli_num_rows($query_productos);
			//CARGAMOS EN MEMORIA
			ob_start();//prepara o guarda en buffer o memmoria lo que esta en el archivo factura.php
			//HACEMOS EL LLAMADO AL ARCHICO factura.php
		    include(dirname('__FILE__').'/factura.php');//la direccion para accederlo AE.1
		    $html = ob_get_clean();//Cargamos todo el html del archivo factura.php

		    //USO DE LIBRERIAS
			// instantiate and use the dompdf class
			$dompdf = new Dompdf();//creando un objeto en pdf q corresponde a la libreria

			$dompdf->loadHtml($html);//accedemos a los diferentes metodos de la libreria
			//hace que se cargue el contedido del pdf
			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('letter', 'portrait');//especificamos el tamaño y la orientacion del papel a imprimir
			// Render the HTML as PDF
			$dompdf->render();//leemos el html para transladarlo al pdf
			// Output the generated PDF to Browser
			$dompdf->stream('factura_'.$noFactura.'.pdf',array('Attachment'=>0));//salida del archivo, le colocamos la variable factura y concatenamos la variable noFactura, el nro de factura
			exit;
		}
	}

?>