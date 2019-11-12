<?php

//VALIDACION FUNCION TIPO DE USUARIO LOGEADO
	session_start();
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2 )
	{
		header("location: ../");//redireccionar
	}
	//*************************************

	include "../conexion.php";

	if (!empty($_POST)) 
	{	//si hay clientes enviar a lista_clientes
		if (empty($_POST['idcliente'])) 
		{
			header("location: lista_clientes.php");
			mysqli_close($conection);//cierre de conexion
		}
		  $idcliente = $_POST['idcliente'];

		 //$query_delete = mysqli_query($conection, "DELETE FROM usuario WHERE idusuario = $idusuario");
		 $query_delete = mysqli_query($conection,"UPDATE cliente SET estatus = 0 WHERE idcliente = $idcliente");
		 mysqli_close($conection);//cierre de conexion

		 //Validacion si se ha ejecutado el query
		 if($query_delete){
		 	header("location: lista_clientes.php");
		 	mysqli_close($conection);//cierre de conexion
		 }else{
		 	echo "Error al eliminar";
		 }

	}

	//obtiene el id que se esta enviando y consulta la bd
	if(empty($_REQUEST['id']))
	{
		header("location: lista_clientes.php");
		mysqli_close($conection);//cierre de conexion
	}else{


		$idcliente = $_REQUEST['id'];//se almacena el valor rescatado de la url en la variable $idcliente

		$query = mysqli_query($conection,"SELECT *
										 FROM cliente 
										 WHERE idcliente= $idcliente");
		mysqli_close($conection);//cierre de conexion
		$result = mysqli_num_rows($query);

		if($result > 0){//si result es mayo a 0 quiere decir que hay registros
			while ($data = mysqli_fetch_array($query)) {
				$nit = $data['nit'];
				$nombre = $data['nombre'];
			}
			}else{
				header("location: lista_clientes.php");	
		}
	}
 ?>



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>

	<title>Eliminar Cliente</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="data_delete">
			<i class="fas fa-user-times fa-7x" style="color: #c84646"></i>
			<br><br><br>
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p></p>
			<p>Nombre del Cliente: <span><?php echo $nombre; ?></span></p>
			<p>C.I.: <span><?php echo $nit; ?></span></p>
		

			<form method="post" action="">
				<input type="hidden" name="idcliente" value="<?php echo $idcliente; ?>">
				<a href="lista_clientes.php" class="btn_cancel">Cancelar</a>
				<button type="submit" class="btn_ok"><i class="fas fa-trash-alt"></i> Eliminar</button>
			</form>		
		</div>


	</section>

	<?php include "includes/footer.php"; ?>
</body>
</html>