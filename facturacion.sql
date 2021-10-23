-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-10-2021 a las 13:36:09
-- Versión del servidor: 10.4.19-MariaDB
-- Versión de PHP: 8.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `facturacion`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_precio_producto` (`n_cantidad` INT, `n_precio` DECIMAL(10,2), `codigo` INT)  BEGIN
    	DECLARE nueva_existencia int;
        DECLARE nuevo_total  decimal(10,2);
        DECLARE nuevo_precio decimal(10,2);
        
        DECLARE cant_actual int;
        DECLARE pre_actual decimal(10,2);
        
        DECLARE actual_existencia int;
        DECLARE actual_precio decimal(10,2);
                
        SELECT precio,existencia INTO actual_precio,actual_existencia FROM producto WHERE codproducto = codigo;
        SET nueva_existencia = actual_existencia + n_cantidad;
        SET nuevo_total = (actual_existencia * actual_precio) + (n_cantidad * n_precio);
        SET nuevo_precio = nuevo_total / nueva_existencia;
        
        UPDATE producto SET existencia = nueva_existencia, precio = nuevo_precio WHERE codproducto = codigo;
        
        SELECT nueva_existencia,nuevo_precio;
        
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_temp` (`codigo` INT, `cantidad` INT, `token_user` VARCHAR(50))  BEGIN
    
        DECLARE precio_actual decimal(10,2);
        SELECT precio INTO precio_actual FROM producto WHERE codproducto = codigo;

        INSERT INTO detalle_temp(token_user,codproducto,cantidad,precio_venta)VALUES(token_user,codigo,cantidad,precio_actual);

        SELECT tmp.correlativo, tmp.codproducto, p.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp
        INNER JOIN producto p
        ON tmp.codproducto = p.codproducto
        WHERE tmp.token_user = token_user;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `anular_factura` (`no_factura` INT)  BEGIN
    	DECLARE existe_factura int;
        DECLARE registros int;
        DECLARE a int;
        
        /*atributos temporales*/
        DECLARE cod_producto int;
        DECLARE cant_producto int;
        DECLARE existencia_actual int;
        DECLARE nueva_existencia int;
        
        /*Consultamos si la factura existe en la bd, contando*/
        SET existe_factura = (SELECT COUNT(*) FROM factura WHERE nofactura = no_factura and estatus = 1);
        
    	/*Validamos la variable*/
        IF existe_factura > 0 THEN
        	CREATE TEMPORARY TABLE tbl_tmp(/*Creamos una tabla temporal*/
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                cod_prod BIGINT,
                cant_prod int);
         		
                /*Luego seteamo la variable a*/
                SET a = 1;
                /*Contamos todos los registros que tenemos en la tabla detalle_factura*/
                SET registros = (SELECT COUNT(*) FROM detallefactura WHERE nofactura = no_factura);
                /*Validamos cuantos registros existen en la variable registros*/
                IF registros > 0 THEN
                	/*extraemos los dos campo(codigo del producto y cantidad) de la tabla detalle_factura*/
                    /*para ingresarlos en la tabal temporal creada, todos los registros q encuentra*/
                    INSERT INTO tbl_tmp(cod_prod,cant_prod) SELECT codproducto,cantidad FROM detallefactura WHERE
                    nofactura = no_factura;/*donde el nro de factura de la tabla detalle = nro fact de la tbl temporal*/
                    /*Recorremos todos los registros hallados en la tabla temporal*/
                    WHILE a <= registros DO/*mientras que registros sea mayor igual a 1 osea si encuentra registros*/
                    /*asignamos los dos valores a la tabla tbl_tmp de las variables temporales*/
                    	SELECT cod_prod,cant_prod INTO cod_producto,cant_producto FROM tbl_tmp WHERE id = 0;
                        /*Extraemos la existencia actual de c/ producto en particular*/
                        /*Asignamos lo que tiene existencia de la tabla producto a la variable tmporal existencia_actual */
                        SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = cod_producto;
                        /*Sumamos la existencia actual con la cantidad de producto que hemos vendido*/
                        SET nueva_existencia = existencia_actual = cant_producto;
                        /*Actualizamos la existencia de cada producto*/
                        UPDATE producto SET existencia = nueva_existencia WHERE codproducto + cod_producto;
                        /*Incrementamos la variable*/
                        SET a=a+1;/*seteamos la variable hasta que se cumpla la validacion de registros while arriba*/
                        
                    END WHILE;
                    /*ACTUALIZAMOS LA FACTURA Y LE SETEAMOS EN EL ESTATUS EL VALOR 2*/
                    UPDATE factura SET estatus = 2 WHERE nofactura = no_factura;
                    /*Limpiamos la tabla temporal*/
                    DROP TABLE tbl_tmp; 
                    /*Mostramos los datos de la factura*/
                    SELECT * FROM factura WHERE nofactura = no_factura;
                    
                END IF;
                	
        ELSE/*sino muestra 0*/
        	SELECT 0 factura;
        END IF;
    
    
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dataDashboard` ()  BEGIN
    	DECLARE usuarios int;
        DECLARE clientes int;
        DECLARE proveedores int;
        DECLARE productos int;
        DECLARE ventas int;
        
        /*con INTO asignamos las variables que tenemos arriba*/
        SELECT COUNT(*) INTO usuarios FROM usuario WHERE estatus !=10;
        SELECT COUNT(*) INTO clientes FROM cliente WHERE estatus !=10;
        SELECT COUNT(*) INTO proveedores FROM proveedor WHERE estatus !=10;
        SELECT COUNT(*) INTO productos FROM producto WHERE estatus!=10;
        SELECT COUNT(*) INTO ventas FROM factura WHERE estatus !=10;
        
        /*para devolver los valores*/
        SELECT usuarios,clientes,proveedores,productos,ventas;
        
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_temp` (`id_detalle` INT, `token` VARCHAR(50))  BEGIN
    	DELETE FROM detalle_temp WHERE correlativo = id_detalle;
        
        SELECT tmp.correlativo, tmp.codproducto,p.descripcion,tmp.cantidad,tmp.precio_venta,tmp.precio_venta
        FROM detalle_temp tmp 
        INNER JOIN producto p ON
        tmp.codproducto = p.codproducto
        WHERE tmp.token_user = token;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_venta` (`cod_usuario` INT, `cod_cliente` INT, `token` VARCHAR(60))  BEGIN
        	DECLARE factura INT;
            DECLARE registros INT;
            DECLARE total DECIMAL(10,2);
            
            DECLARE nueva_existencia int;
            DECLARE existencia_actual int;
            
            DECLARE tmp_cod_producto int;
            DECLARE tmp_cant_producto int;
            DECLARE a INT;
            SET a = 1;

        	CREATE TEMPORARY TABLE tbl_tmp_tokenuser(
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                cod_prod BIGINT,/*almacenamos el codigo del producto temporalmente*/
                cant_prod int);/*almacenamos la cantidad del producto temporalmente*/

            SET registros = (SELECT COUNT(*) FROM detalle_temp WHERE token_user = token);
            IF registros > 0 THEN
            	INSERT INTO tbl_tmp_tokenuser(cod_prod, cant_prod) SELECT codproducto,cantidad FROM detalle_temp WHERE token_user = token;
                INSERT INTO factura(usuario,codcliente) VALUES(cod_usuario,cod_cliente);
                SET factura = LAST_INSERT_ID();/*ref B1*/

                INSERT INTO detallefactura(nofactura,codproducto,cantidad,precio_venta)
                		SELECT (factura) as nofactura,codproducto,cantidad,precio_venta FROM detalle_temp
                        WHERE token_user = token;
             
                        WHILE a <= registros DO

               
                        	SELECT cod_prod,cant_prod INTO tmp_cod_producto,tmp_cant_producto FROM tbl_tmp_tokenuser
                            WHERE id = a;
        
                            SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = tmp_cod_producto;
        
                            SET nueva_existencia = existencia_actual - tmp_cant_producto;
                            UPDATE producto SET existencia = nueva_existencia WHERE codproducto = tmp_cod_producto;
                            SET a=a+1;
                        
                        END WHILE;

                        SET total = (SELECT SUM(cantidad * precio_venta) FROM detalle_temp WHERE token_user = token);

                        UPDATE factura SET totalfactura = total WHERE nofactura = factura;
                        DELETE FROM detalle_temp WHERE token_user = token;
                        TRUNCATE TABLE tbl_tmp_tokenuser;
      
                        SELECT * FROM factura WHERE nofactura = factura;
                
            ELSE

            SELECT 0;
            
            END IF;
        END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL,
  `nit` int(11) DEFAULT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `telefono` int(11) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `dateadd` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `nit`, `nombre`, `telefono`, `direccion`, `dateadd`, `usuario_id`, `estatus`) VALUES
(1, 9999999, 's/n', 878766787, 's/n', '2018-02-15 21:55:51', 1, 1),
(2, 87654321, 'Marta Gonzales', 34343434, 'Calzada Buena Vista', '2018-02-15 21:57:03', 1, 1),
(3, 0, 'Elena HernÃ¡ndez', 987897987, 'Guatemala, Chimaltenango', '2018-02-15 21:59:20', 2, 0),
(4, 0, 'Julio Maldonado', 908098979, 'Avenida las Americas Zona 14', '2018-02-15 22:00:31', 3, 0),
(5, 0, 'Helen', 98789798, 'Guatemala', '2018-02-18 10:53:53', 1, 1),
(6, 0, 'Juan', 7987987, 'Chimaltenango', '2018-02-18 10:56:44', 1, 0),
(7, 798798798, 'Jorge Maldonado', 2147483647, 'Colonia la Flores', '2018-02-18 11:10:07', 1, 1),
(8, 0, 'Marta Cabrera', 987987987, 'Guatemala', '2018-02-18 11:11:40', 2, 1),
(9, 79879879, 'Julio Estrada', 897987987, 'Avenida Elena', '2018-02-18 11:13:23', 3, 1),
(10, 2147483647, 'Roberto Morazan', 2147483647, 'Chimaltenango, Guatemala', '2018-03-04 19:17:22', 1, 1),
(11, 898798798, 'Rosa Pineda', 987998788, 'Ciudad Quetzal', '2018-03-04 19:17:45', 1, 1),
(12, 0, 'Angel Molina', 2147483647, 'Calzada Buena Vista', '2018-03-04 19:18:21', 1, 1),
(13, 5645646, 'jose', 4164556, 'calle 27', '2021-08-07 10:05:47', 1, 1),
(14, 123456, 'Juan', 465646, 'calle 13', '2021-08-27 15:18:45', 1, 1),
(15, 0, '56464', 5494989, 'calle 12', '2021-08-27 15:25:22', 1, 1),
(16, 5555, 'prueba', 215616, 'calle p', '2021-08-27 15:28:06', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` bigint(20) NOT NULL,
  `ci` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `telefono` bigint(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `direccion` text NOT NULL,
  `iva` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `ci`, `nombre`, `razon_social`, `telefono`, `email`, `direccion`, `iva`) VALUES
(1, '484898', 'pc servicios', '', 51661, 'ifo@correo.com', 'toba 13', '10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefactura`
--

CREATE TABLE `detallefactura` (
  `correlativo` bigint(11) NOT NULL,
  `nofactura` bigint(11) DEFAULT NULL,
  `codproducto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `detallefactura`
--

INSERT INTO `detallefactura` (`correlativo`, `nofactura`, `codproducto`, `cantidad`, `precio_venta`) VALUES
(1, 1, 3, 1, '250.00'),
(2, 1, 4, 1, '10000.00'),
(4, 2, 1, 1, '110.00'),
(5, 2, 2, 1, '16000.00'),
(7, 3, 1, 1, '110.00'),
(8, 3, 2, 3, '16000.00'),
(9, 4, 1, 1, '110.00'),
(10, 4, 2, 1, '16000.00'),
(11, 4, 3, 1, '250.00'),
(12, 5, 5, 1, '500.00'),
(13, 6, 3, 1, '250.00'),
(14, 7, 8, 1, '160.00'),
(15, 8, 4, 1, '10000.00'),
(16, 9, 3, 1, '250.00'),
(17, 10, 5, 1, '500.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `correlativo` int(11) NOT NULL,
  `token_user` varchar(50) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas`
--

CREATE TABLE `entradas` (
  `correlativo` int(11) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `entradas`
--

INSERT INTO `entradas` (`correlativo`, `codproducto`, `fecha`, `cantidad`, `precio`, `usuario_id`) VALUES
(1, 1, '0000-00-00 00:00:00', 150, '110.00', 1),
(2, 2, '2018-04-05 00:12:15', 100, '1500.00', 1),
(3, 3, '2018-04-07 22:48:23', 200, '250.00', 9),
(4, 4, '2018-09-08 22:28:50', 50, '10000.00', 1),
(5, 5, '2018-09-08 22:34:38', 100, '500.00', 1),
(6, 6, '2018-09-08 22:35:27', 8, '2000.00', 1),
(7, 7, '2018-12-02 00:15:09', 75, '2200.00', 1),
(8, 8, '2018-12-02 00:39:42', 75, '160.00', 1),
(9, 9, '2021-08-07 10:07:17', 3, '2656.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `nofactura` bigint(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) DEFAULT NULL,
  `codcliente` int(11) DEFAULT NULL,
  `totalfactura` decimal(10,2) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`nofactura`, `fecha`, `usuario`, `codcliente`, `totalfactura`, `estatus`) VALUES
(1, '2021-08-07 11:44:01', 18, 1, '10250.00', 1),
(2, '2021-08-07 11:47:01', 18, 1, '16110.00', 1),
(3, '2021-08-07 11:58:41', 18, 1, '48110.00', 1),
(4, '2021-10-01 09:56:37', 18, 1, '16360.00', 1),
(5, '2021-10-01 09:57:16', 18, 1, '500.00', 1),
(6, '2021-10-01 10:27:04', 18, 1, '250.00', 1),
(7, '2021-10-01 10:27:46', 18, 1, '160.00', 1),
(8, '2021-10-01 10:29:02', 18, 1, '10000.00', 1),
(9, '2021-10-01 10:30:03', 18, 1, '250.00', 1),
(10, '2021-10-01 10:41:40', 18, 1, '500.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `codproducto` int(11) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `proveedor` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `existencia` int(11) DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1,
  `foto` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codproducto`, `descripcion`, `proveedor`, `precio`, `existencia`, `date_add`, `usuario_id`, `estatus`, `foto`) VALUES
(1, 'Mouse USB', 11, '110.00', 147, '2018-04-05 00:09:34', 1, 1, 'img_producto.png'),
(2, 'Monitor LCD', 3, '16000.00', 95, '2018-04-05 00:12:15', 1, 1, 'img_producto.png'),
(3, 'Teclado USB', 9, '250.00', 196, '2018-04-07 22:48:23', 9, 1, 'img_producto.png'),
(4, 'Cama', 5, '10000.00', 48, '2018-09-08 22:28:50', 1, 1, 'img_21084f55f7b61c8baa2726ad0b4a1dca.jpg'),
(5, 'Plancha', 6, '500.00', 98, '2018-09-08 22:34:38', 1, 1, 'img_25c1e2ae283b99e83b387bf800052939.jpg'),
(6, 'Monitor', 11, '2000.00', 8, '2018-09-08 22:35:27', 1, 1, 'img_producto.png'),
(7, 'Monitor LCD 17', 9, '2200.00', 75, '2018-12-02 00:15:09', 1, 1, 'img_1328286905ecc9eec8e81b94fa1786b9.jpg'),
(8, 'USG 32 GB', 11, '160.00', 74, '2018-12-02 00:39:42', 1, 1, 'img_cce86641de32660a29e0fa49f58a950c.jpg'),
(9, 'mueble', 7, '2656.00', 3, '2021-08-07 10:07:17', 1, 1, 'img_producto.png');

--
-- Disparadores `producto`
--
DELIMITER $$
CREATE TRIGGER `entradas_A_I` AFTER INSERT ON `producto` FOR EACH ROW BEGIN
		INSERT INTO entradas(codproducto,cantidad,precio,usuario_id) 
		VALUES(new.codproducto,new.existencia,new.precio,new.usuario_id);    
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `codproveedor` int(11) NOT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` bigint(11) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`codproveedor`, `proveedor`, `contacto`, `telefono`, `direccion`, `date_add`, `usuario_id`, `estatus`) VALUES
(1, 'BIC', 'Claudia Rosales', 789877889, 'Avenida las Americas', '2018-03-20 23:13:43', 1, 0),
(2, 'CASIO', 'Jorge Herrera', 565656565656, 'Calzada Las Flores', '2018-03-20 23:14:41', 2, 0),
(3, 'Omega', 'Julio Estrada', 982877489, 'Avenida Elena Zona 4, Guatemala', '2018-03-24 23:21:10', 1, 1),
(4, 'Dell Compani', 'Roberto Estrada', 2147483647, 'Guatemala, Guatemala', '2018-03-24 23:21:59', 1, 1),
(5, 'Olimpia S.A', 'Elena Franco Morales', 564535676, '5ta. Avenida Zona 4 Ciudad', '2018-03-24 23:22:45', 1, 1),
(6, 'Oster', 'Fernando Guerra', 78987678, 'Calzada La Paz, Guatemala', '2018-03-24 23:24:43', 1, 1),
(7, 'ACELTECSA S.A', 'Ruben PÃ©rez', 789879889, 'Colonia las Victorias', '2018-03-24 23:25:39', 1, 1),
(8, 'Sony', 'Julieta Contreras', 89476787, 'Antigua Guatemala', '2018-03-24 23:26:45', 1, 1),
(9, 'VAIO', 'Felix Arnoldo Rojas', 476378276, 'Avenida las Americas Zona 13', '2018-03-24 23:30:33', 1, 1),
(10, 'SUMAR', 'Oscar Maldonado', 788376787, 'Colonia San Jose, Zona 5 Guatemala', '2018-03-24 23:32:28', 1, 1),
(11, 'HP', 'Angel Cardona', 2147483647, '5ta. calle zona 4 Guatemala', '2018-03-24 23:52:20', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idrol` int(11) NOT NULL,
  `rol` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idrol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Supervisor'),
(3, 'Vendedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `usuario` varchar(15) DEFAULT NULL,
  `clave` varchar(100) DEFAULT NULL,
  `rol` int(11) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`, `estatus`) VALUES
(1, 'Abel', 'info@abelosh.com', 'admin', '202cb962ac59075b964b07152d234b70', 1, 1),
(2, 'Julio Estrada', 'julio@gmail.com', 'julio', 'c027636003b468821081e281758e35ff', 2, 1),
(3, 'Carlos HernÃ¡ndez', 'carlos@gmail.com', 'carlos', 'dc599a9972fde3045dab59dbd1ae170b', 3, 1),
(5, 'Marta Elena Franco', 'marta@gmail.com', 'marta', 'a763a66f984948ca463b081bf0f0e6d0', 3, 1),
(7, 'Carol Cabrera', 'carol@gmail.com', 'carol', 'a9a0198010a6073db96434f6cc5f22a8', 2, 0),
(8, 'Marvin Solares ', 'marvin@gmail.com', 'marvin', 'dba0079f1cb3a3b56e102dd5e04fa2af', 3, 1),
(9, 'Alan Melgar', 'alan@gmail.com', 'alan', '02558a70324e7c4f269c69825450cec8', 2, 1),
(10, 'Efrain GÃ³mez', 'efrain@gmail.com', 'efrain', '69423f0c254e5c1d2b0f5ee202459d2c', 2, 1),
(11, 'Fran Escobar', 'fran@gmail.com', 'fran', '2c20cb5558626540a1704b1fe524ea9a', 1, 1),
(12, 'Hana Montenegro', 'hana@gmail.com', 'hana', '52fd46504e1b86d80cfa22c0a1168a9d', 3, 1),
(13, 'Fredy Miranda', 'fredy@gmail.com', 'fredy', 'b89845d7eb5f8388e090fcc151d618c8', 2, 1),
(14, 'Roberto Salazar', 'roberto@hotmail.com', 'roberto', 'c1bfc188dba59d2681648aa0e6ca8c8e', 3, 1),
(15, 'William Fernando PÃ©rez', 'william@hotmail.com', 'william', 'fd820a2b4461bddd116c1518bc4b0f77', 3, 1),
(16, 'Francisco Mora', 'frans@gmail.com', 'frans', '64dd0133f9fb666ca6f4692543844f31', 3, 1),
(17, 'Ruben Guevara', 'ruben@hotmail.es', 'ruben', '32252792b9dccf239f5a5bd8e778dbc2', 3, 1),
(18, 'Wilson', 'wilmiltoss@gmail.com', 'wmiltos', 'e10adc3949ba59abbe56e057f20f883e', 1, 1),
(19, 'Cyntia Fleitas', 'cfleitas@gmail.com', 'cfleitas', '202cb962ac59075b964b07152d234b70', 3, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codproducto`),
  ADD KEY `nofactura` (`nofactura`);

--
-- Indices de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `nofactura` (`token_user`),
  ADD KEY `codproducto` (`codproducto`);

--
-- Indices de la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codproducto`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`nofactura`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `codcliente` (`codcliente`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codproducto`),
  ADD KEY `proveedor` (`proveedor`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`codproveedor`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idrol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`),
  ADD KEY `rol` (`rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `correlativo` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `nofactura` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `codproducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `codproveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD CONSTRAINT `detallefactura_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`),
  ADD CONSTRAINT `detallefactura_ibfk_3` FOREIGN KEY (`nofactura`) REFERENCES `factura` (`nofactura`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD CONSTRAINT `detalle_temp_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD CONSTRAINT `entradas_ibfk_1` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`codcliente`) REFERENCES `cliente` (`idcliente`),
  ADD CONSTRAINT `factura_ibfk_3` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`proveedor`) REFERENCES `proveedor` (`codproveedor`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `proveedor_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `rol` (`idrol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
