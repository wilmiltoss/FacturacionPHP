		<nav>
			<ul>
				<li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
			<?php 
			//Muestra el menu usuario solo al administrador
				if($_SESSION['rol'] == 1){
			?>
				<li class="principal">	

					<a href="#"> <i class="fas fa-users"></i> Usuarios</a>
					<ul>
						<li><a href="registro_usuario.php"><i class="fas fa-user-plus"></i> Nuevo Usuario</a></li>
						<li><a href="lista_usuarios.php"> <i class="fas fa-address-book"></i> Lista de Usuarios</a></li>
					</ul>
				</li>
			<?php } ?>
				<li class="principal">
					<a href="#"><i class="fas fa-users"></i> Clientes</a>
					<ul>
						<li><a href="registro_cliente.php"> <i class="fas fa-user-plus"></i> Nuevo Cliente</a></li>
						<li><a href="lista_clientes.php"> <i class="fas fa-address-book"></i> Lista de Clientes</a></li>
					</ul>
				<?php 
					if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
				 ?>
				</li>
				
				<li class="principal">
					<a href="#"><i class="fas fa-truck"></i> Proveedores</a>
					<ul>
						<li><a href="registro_proveedor.php"> <i class="fas fa-plus"></i> Nuevo Proveedor</a></li>
						<li><a href="lista_proveedor.php"><i class="fas fa-clipboard-list"></i> Lista de Proveedores</a></li>
					</ul>  
				</li>
				
				<li class="principal">
					<a href="#"><i class="fas fa-box-open"></i> Productos</a>
					<ul>
						<li><a href="registro_producto.php"><i class="fas fa-plus"></i> Nuevo Producto</a></li>
						<li><a href="lista_producto.php"><i class="fas fa-clipboard-list"></i> Lista de Productos</a></li>
					</ul>
				</li>
			<?php } ?>
				<li class="principal">
					<a href="#"><i class="far fa-file-alt"></i> Ventas</a>
					<ul>
						<li><a href="nueva_venta.php"><i class="fas fa-plus"></i> Nueva Venta</a></li>
						<li><a href="ventas.php"><i class="far fa-file-alt"></i> Ventas</a></li>
					</ul>
				</li>
			</ul>
		</nav>