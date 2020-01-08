<?php 

	if(empty($_SESSION['active']))
	{
		header('location: ../');
	}
?>
	<header>
		<div class="header">			
			<h1>Sistema Facturación</h1>
			<div class="optionsBar">
				<p>Asuncion, <?php echo fechaC(); ?></p>
				<span>|</span>
				<span class="user"><?php echo $_SESSION['user'].' -'.$_SESSION['rol'].' -'.$_SESSION['email']; ?></span>
				<img class="photouser" src="img/user.png" alt="Usuario">
				<a href="salir.php"><img class="close" src="img/salir.png" alt="Salir del sistema" title="Salir"></a>
			</div>
		</div>
<?php include "nav.php"; ?>
</header>
 <!-- Clase modal generico para la ventanita de alerta--> 
 <!-- event.preventDefault() evita q se recargue el formulario --> 
<div class="modal">
	<div class="bodyModal">
	</div>
</div>