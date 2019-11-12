<?php 
	$host = 'localhost';
	$user = 'root';
	$password = '';
	$db = 'facturacion';

	$conection = @mysqli_connect($host,$user,$password,$db);
	//mysql_close($conection);

	if(!$conection){
		echo "Error en la conexion";
	}else{
		//echo "Conexion exitosa";
	}



 ?>