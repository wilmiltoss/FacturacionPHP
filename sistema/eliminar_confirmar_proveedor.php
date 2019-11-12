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
		if (empty($_POST['idproveedor'])) 
		{
			header("location: lista_proveedor.php");
			mysqli_close($conection);//cierre de conexion
		}
		  $idproveedor = $_POST['idproveedor'];

		 //$query_delete = mysqli_query($conection, "DELETE FROM usuario WHERE idusuario = $idusuario");
		 $query_delete = mysqli_query($conection,"UPDATE proveedor SET estatus = 0 WHERE codproveedor = $idproveedor");
		 mysqli_close($conection);//cierre de conexion

		 //Validacion si se ha ejecutado el query
		 if($query_delete){
		 	header("location: lista_proveedor.php");
		 	mysqli_close($conection);//cierre de conexion
		 }else{
		 	echo "Error al eliminar";
		 }

	}

	//obtiene el id que se esta enviando y consulta la bd
	if(empty($_REQUEST['id']))
	{
		header("location: lista_proveedor.php");
		mysqli_close($conection);//cierre de conexion
	}else{


		$idproveedor = $_REQUEST['id'];//se almacena el valor rescatado de la url en la variable $idcliente

		$query = mysqli_query($conection,"SELECT *
										 FROM proveedor 
										 WHERE codproveedor= $idproveedor");
		mysqli_close($conection);//cierre de conexion
		$result = mysqli_num_rows($query);

		if($result > 0){//si result es mayo a 0 quiere decir que hay registros
			while ($data = mysqli_fetch_array($query)) {
				$proveedor = $data['proveedor'];
			}
			}else{
				header("location: lista_proveedor.php");	
		}
	}
 ?>



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>

	<title>Eliminar Proveedor</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="data_delete">
			<i class="fas fa-building fa-7x" style="color: #c84646"></i>
			<br><br><br>
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p></p>
			<p>Nombre del Proveedor: <span><?php echo $proveedor; ?></span></p>
		

			<form method="post" action="">
				<input type="hidden" name="idproveedor" value="<?php echo $idproveedor; ?>">
				<a href="lista_proveedor.php" class="btn_cancel"> <i class="fas fa-ban"></i> Cancelar</a>
				<button type="submit" class="btn_ok"><i class="fas fa-trash-alt"></i> Eliminar</button>
			</form>		
		</div>


	</section>

	<?php include "includes/footer.php"; ?>
</body>
</html>