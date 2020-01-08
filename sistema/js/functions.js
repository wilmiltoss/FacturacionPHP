
//READY = cuando se carga todo el documento se cargan los scrips siguientes
 $(document).ready(function(){

    //--------------------- SELECCIONAR FOTO PRODUCTO ---------------------
    $("#foto").on("change",function(){
    	var uploadFoto = document.getElementById("foto").value;
        var foto       = document.getElementById("foto").files;
        var nav = window.URL || window.webkitURL;
        var contactAlert = document.getElementById('form_alert');
        
            if(uploadFoto !='')
            {
                var type = foto[0].type;
                var name = foto[0].name;
                if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png')
                {
                    contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';                        
                    $("#img").remove();
                    $(".delPhoto").addClass('notBlock');
                    $('#foto').val('');
                    return false;
                }else{  
                        contactAlert.innerHTML='';
                        $("#img").remove();
                        $(".delPhoto").removeClass('notBlock');
                        var objeto_url = nav.createObjectURL(this.files[0]);
                        $(".prevPhoto").append("<img id='img' src="+objeto_url+">");
                        $(".upimg label").remove();
                        
                    }
              }else{
              	alert("No selecciono foto");
                $("#img").remove();
              }              
    });
    //ACCION QUE REMUEVE LA FOTO F.1
    $('.delPhoto').click(function(){
    	$('#foto').val('');
    	$(".delPhoto").addClass('notBlock');
    	$("#img").remove();
      //Validacion remocion de la foto, cambiarle el nombre si es eliminado para q le aparesca la foto por defecto G.1
      if($("#foto_actual") && $("#foto_remove")){//si los dos elemento existen
          $("#foto_remove").val('img_producto.png'); //colocamos a el elemento #foto_remove la descripcion de img_producto.png
      }

    });

    //Modal Form Add Product
    //evento a la clase add_product //ventanita
    $(".add_product").click(function(e){

        e.preventDefault(); 
        var producto = $(this).attr('product');// attr permite accceder a los atributos de los elementos
        var action = 'infoProducto';

        //ajax
        $.ajax({
            url: 'ajax.php',
            type: 'POST',//se envia por el metodo post
            async: true, //ejecucion de manera asincrona
            data:{action:action,producto:producto},//datos que enviamos al archivo ajax.php`
      
            success: function(response){
            console.log(response);//observamos en consola los datos que devuelve

            //si no hay error de la consulta del ajax.php
            if(response != 'error'){
                //convertimos  a un objeto el formato json para imprimir y acceder con javascrip
           
               //var  info = JSON.parse(JSON.stringify(response));
                //var info  = JSON.stringify(response); no anda
                //var info  = jQuery.parseJSON(response);
                var info = JSON.parse(response);
            

                console.log(info);//imprimimos el info

                //mostramos en pantalla
                // $('#producto_id').val(info.codproducto);
                 //$('.nameProducto').html(info.descripcion);

                 //FORMULARIO DE AJAX AGREGAR  //<!--B.1 -->
                 $('.bodyModal').html('<form action="" method="post" name="form_add_product" id="form_add_product" onsubmit="event.preventDefault(); sendDataProduct();">'+
                                      '<h1> <i class="fas fa-cubes" style="font-size: 45pt;"></i> <br> Agregar Producto</h1>'+
                                      '<h2 class="nameProducto">'+info.descripcion+'</h2><br>'+
                                      '<input type="number" name="cantidad" id="txtCantidad" placeholder="Cantidad del Producto" required><br>'+
                                      '<input type="text" name="precio" id="txtPrecio" placeholder="Precio del Producto"required>'+
                                      '<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'"required>'+
                                      '<input type="hidden" name="action" value="addProduct" required>'+
                                      '<div class="alert alertAddProduct"></div>'+
                                      '<button class="submit" class="btn_new"><i class="fas fa-plus"></i> Agregar</button>'+
                                      '<a href="#" class="btn_ok closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>'+

                                   '</form>');

               }

             },

            //success: function(error){
            //console.log(error);   
             //} 
        });
       //muestra el id de c/u de los productos
        $('.modal').fadeIn(); 
    });

    //MODAL FORM DELETE PRODUCT
    $(".del_product").click(function(e){

        e.preventDefault(); 
        var producto = $(this).attr('product');// attr permite accceder a los atributos de los elementos
        var action = 'infoProducto';//esta extrayendo la informacion del producto

        //ajax
        $.ajax({
            url: 'ajax.php',
            type: 'POST',//se envia por el metodo post
            async: true, //ejecucion de manera asincrona
            data:{action:action,producto:producto},//datos que enviamos al archivo ajax.php`
      
            success: function(response){
            console.log(response);//observamos en consola los datos que devuelve

            //si no hay error de la consulta del ajax.php
            if(response != 'error'){
                //convertimos  a un objeto el formato json para imprimir y acceder con javascrip
           
               //var  info = JSON.parse(JSON.stringify(response));
                //var info  = JSON.stringify(response); no anda
                //var info  = jQuery.parseJSON(response);
                var info = JSON.parse(response);
            

                console.log(info);//imprimimos el info

                //mostramos en pantalla
                // $('#producto_id').val(info.codproducto);
                 //$('.nameProducto').html(info.descripcion);

                 //FORMULARIO DE AJAX eliminar  //<!--J.1 -- en base a la funcion delProduct(), eliminamos el registro k.1>
                 $('.bodyModal').html('<form action="" method="post" name="form_del_product" id="form_del_product" onsubmit="event.preventDefault(); delProduct();">'+
                                      '<h1> <i class="fas fa-cubes" style="font-size: 45pt;"></i> <br> Eliminar Producto</h1>'+//N.1
                                      '<p>¿Está seguro de eliminar el siguiente registro?</p>'+
                                      '<h2 class="nameProducto">'+info.descripcion+'</h2><br>'+

                                      '<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'"required>'+//M.1
                                      '<input type="hidden" name="action" value="delProduct" required>'+
                                      '<div class="alert alertAddProduct"></div>'+
                                      '<a href="#" class="btn_cancel" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>'+
                                      '<button type="submit" class="btn_ok"><i class="fas fa-trash-alt"></i> Eliminar</button>'+
                                   '</form>');

               }

             },

            //success: function(error){
            //console.log(error);   
             //} 
        });
       //muestra el id de c/u de los productos
        $('.modal').fadeIn(); 
    });
    //scrip p/ crear el evento del select, search_proveedor LL.1, change= ejecuta una funcion
    $('#search_proveedor').change(function(e) {
      e.preventDefault();

      var sistema = getUrl();
      //alert(sistema);
      //Direccionamos con location.href a la url q tiene la variable sistema y concatena con el archivo buscar_producto.php
      location.href = sistema+'buscar_producto.php?proveedor='+$(this).val();//this=este elemento hace el select
    });

    //Activa campos para regis trar cliente Q.1
    $('.btn_new_cliente').click(function(e) {//seteamos el vento click con una funcion
      e.preventDefault();
      //a los campos removemos el atributo disable 
      $('#nom_cliente').removeAttr('disabled');
      $('#tel_cliente').removeAttr('disabled');
      $('#dir_cliente').removeAttr('disabled');
      //mostramos el div donde se encuentra el boton guardar R.1
      $('#div_registro_cliente').slideDown();
    });


    //Buscar Cliente            //evento keyup al presionar la tecla 
    $('#ci_cliente').keyup(function(e) { //colocamos el id del elemento, el id se trae del formulario 
      e.preventDefault(); //evita recargar la pagina
      //capturamos el valor que escribimos en el campo, this quiere decir de este elemento
      var cl = $(this).val();
      var action = 'searchCliente'; //S.1
      //ejecutamos un ajax
      $.ajax({
        url: 'ajax.php',//se dirige a este archivo
        type: "POST",
        async : true,
        data: {action:action,cliente:cl},//llevan las variables al ajax.php
        success: function(response)
        {
            //console.log(response);//muestra informacion en la consola
            if (response == 0) {//si devuelve 0
              //colocamos en blanco todos los campos
              $('#idcliente').val('');
              $('#nom_cliente').val('');
              $('#tel_cliente').val('');
              $('#dir_cliente').val('');
              //Mostrar boton agregar
              $('.btn_new_cliente').slideDown();//y mostramos el boton para crear un nuevo cliente
            }else{
              var data = $.parseJSON(response);//parseamos el formato json
              //colocamos los datos en los campos
              $('#idcliente').val(data.idcliente);//obtenemos el dato cliente
              $('#nom_cliente').val(data.nombre); //obtenemos el dato nombre
              $('#tel_cliente').val(data.telefono);
              $('#dir_cliente').val(data.direccion);
              //Ocultar boton agregar
              $('.btn_new_cliente').slideUp();

              //Bloquea campos
              $('#nom_cliente').attr('disabled', 'disabled');
              $('#tel_cliente').attr('disabled', 'disabled');
              $('#dir_cliente').attr('disabled', 'disabled');

              //Oculta boton guardar
              $('#div_registro_cliente').slideUp();
            }
        },
        error: function(error){

        }
      });
      
    });

       //Crear Clientes -Ventas T.1
    $('#form_new_cliente_venta').submit(function(e) {//T.1
       e.preventDefault();//previene que se recargue el archivo 

       $.ajax({
        url: 'ajax.php',//se dirige a este archivo
        type: "POST",
        async : true,
        data: $('#form_new_cliente_venta').serialize(),//llevan todos los datos de las variables al ajax.php

        success: function(response)
        {
            //console.log(response);//muestra informacion en la consola
            //Validamos lo que nos ha devuelto el ajax.php /Registrar Clientes - Ventas
              if (response != 'error') {//si no devuelve error
              //colocamos el id al input idcliente hidden que esta oculto, le seteamos el valor para obtener el id 
              $('#idcliente').val(response);
              //bloqueamos los campos
              $('#nom_cliente').attr('disabled', 'disabled');
              $('#tel_cliente').attr('disabled', 'disabled');
              $('#dir_cliente').attr('disabled', 'disabled');

              //Ocultamos el boton agregar
              $('.btn_new_cliente').slideUp();//btn_new_cliente lo traemos del formulario
              //Ocultamos el boton guardar
              $('#div_registro_cliente').slideUp();
      
              }
        },
        error: function(error){

        }
      });  
    }); 

    //BUSCAR PRODUCTO U.1
    $('#txt_cod_producto').keyup(function(e) {//U.1
       e.preventDefault();//previene que se recargue el archivo 
       var producto = $(this).val(); //captura el valor del elemento del txt_cod_producto
       var action = 'infoProducto'; 

       if (producto != '') //validamos si la variable producto si no tiene elementos
       { 
         $.ajax({
          url: 'ajax.php',//se dirige a este archivo
          type: "POST",
          async : true,
          data: {action:action,producto:producto}, //formato json, va lo que vayamos a enviar, la variable declarada arriba

          success: function(response)
          {
            //console.log(response);
            if (response != 'error') //si es diferente de error
             {
               var info = JSON.parse(response); //parseamos la respuesta para convertirlo en un objeto para luego manipularlo
               $('#txt_descripcion').html(info.descripcion);//seteamos la descripcion en html
               $('#txt_existencia').html(info.existencia);
               $('#txt_cant_producto').val('1');//valor por defecto lo ponemos 1
               $('#txt_precio').html(info.precio);//seteamos el precio
               $('#txt_precio_total').html(info.precio);//seteamos el precio total

               //Activar el campo Cantidad, removemos el disabled que se puso en el html formulario
               $('#txt_cant_producto').removeAttr('disabled');

               //Mostrar boton Agregar, cuando tengamos los datos del producto
               $('#add_product_venta').slideDown();
             }else{
              //de lo contrario limpiamos todos los campos
              $('#txt_descripcion').html('-');
              $('#txt_existencia').html('-');
              $('#txt_cant_producto').val('0');
              $('#txt_precio').html('0.00');
              $('#txt_precio_total').html('0.00');

              //Bloquear Cantidad
              $('#txt_cant_producto').attr('disabled', 'disabled');

              //Ocultar boton agregar
              $('#add_product_venta').slideUp(); 
             }
             

          },
          error: function(error){
        }
      });  
     }
    }); 

    //VALIDAR CANTIDAD DE PRODUCTO ANTES DE AGREGAR

    $('#txt_cant_producto').keyup(function(e) {//txt_cant_producto = elemento a quien le estamos agregando el evento keyup
      e.preventDefault();
      //toma el valor del campo del elemento txt_cant_producto y lo multiplica por el precio que esta en el txt del html
      var precio_total = $(this).val() * $('#txt_precio').html();
      var existencia = parseInt($('#txt_existencia').html());//obtenemos el valor de la celda por medio del elemento #txt_existencia y .html
                                                  //convertimos un string a entero por medio de parseInt
      $('#txt_precio_total').html(precio_total);//colocamos en el elemento txt_precio_total la variable precio_total

      //Oculta el boton agregar si la cantidad cargada es menor que 1 o es mayor a la existencia
      if ( ($(this).val() < 1 || isNaN($(this).val())) || ($(this).val() > existencia)  ) { //isNaN valida si es numero el campo cargado
         $('#add_product_venta').slideUp();//oculta el elemento add_product_venta
      }else{
        $('#add_product_venta').slideDown(); //muestra la opcion de agregar 
      }
    });

  
   //AGREGAR PRODUCTO AL DETALLE
    $("#add_product_venta").click(function(e){//colocamos el id del elemento del formulario al darle clik ejecutamos V.1
        e.preventDefault(); 

        if ($('#txt_cant_producto').val() > 0) //evaluamos el campo cantidad que sea mayor a 0 
        {
          //esto lo que va mostrar en la consola como un array
        var codproducto = $('#txt_cod_producto').val();//guardamos en una variable lo que cargamos en el campo del formulario
        var cantidad    = $('#txt_cant_producto').val();
        var action      = 'addProductoDetalle';// W.1

        //ajax
        $.ajax({
            url: 'ajax.php',
            type: 'POST',//se envia por el metodo post
            async: true, //ejecucion de manera asincrona
            data:{action:action,producto:codproducto,cantidad:cantidad},//datos que enviamos al archivo ajax.php`
      
            success: function(response)
            {
              //console.log(response);//observamos en consola los datos que devuelve
              if (response != 'error') //si el listado que obtenemos del ajax.php es distino a error ref Y.1
              {
                var info = JSON.parse(response);//convertimos el formato json a un objeto, otra vez
                //manipulamos los objetos devueltos en la variable info
                $('#detalle_venta').html(info.detalle);//colocamos el id #detalle_venta(campo), la info.detalle rescatado del objeto
                $('#detalle_totales').html(info.totales);//colocamos el id #detalle_totales(campo), la info.detalle rescatado del objeto

                //luego limpiamos todos los campos- SETEAMOS
                $('#txt_cod_producto').val('');
                $('#txt_descripcion').html('-');
                $('#txt_existencia').html('-');
                $('#txt_cant_producto').val('0');
                $('#txt_precio').html('0.00');
                $('#txt_precio_total').html('0.00');

                //Bloqueamos el campo cantidad, seteamos 
                $('#txt_cant_producto').attr('disabled', 'disabled');
                //Ocultamos la accion agregar
                $('#add_product_venta').slideUp();//con slideUp ocultamos

              }else{
                console.log('no hay datos');
              }
              viewProcesar();//funcion mostrar/ocultar boton procesar
            },
            error: function(error) {
            }

        });
      }

    });

    //ANULAR VENTA
     $("#btn_anular_venta").click(function(e){ //elemento id #btn_anular_venta con el evento click ejecuta la funcion mas abajo AC.1
        e.preventDefault(); 

        //contamos cuantas filas tiene el elemento detalle_venta
        var rows = $('#detalle_venta tr').length;//
        if (rows > 0) // si es mayor a 0
        {
          var action = 'anularVenta';//AC.1 enviamos esto al ajax.php
          //ajax
          $.ajax({
              url: 'ajax.php',
              type: 'POST',//se envia por el metodo post
              async: true, //ejecucion de manera asincrona
              data:{action:action},//datos que enviamos al archivo ajax.php`
        
              success: function(response)
              {
                //console.log(response);
                if (response != 'error') 
                {
                  location.reload(); //refrescamos la pagina con reload   
                }
              },
              error: function(error) {
              }

          });
      }

    });

     //PROCESAR VENTA
     $("#btn_facturar_venta").click(function(e){ //elemento id #btn_facturar_venta con el evento click ejecuta la funcion 
        e.preventDefault(); 

        //contamos cuantas filas tiene el elemento detalle_venta
        var rows = $('#detalle_venta tr').length;//con length cuenta cuantas filas hay
        if (rows > 0) // si es mayor a 0
        {
          var action = 'procesarVenta';//AD.1 enviamos esto al ajax.php
          var codcliente = $('#idcliente').val();//almacena el valor que tiene el elemento idcliente del campo CI del formulario

          //ajax
          $.ajax({
              url: 'ajax.php',
              type: 'POST',//se envia por el metodo post
              async: true, //ejecucion de manera asincrona
              data:{action:action,codcliente:codcliente},//datos que enviamos al archivo ajax.php`
        
              success: function(response)
              {
                  //console.log(response);
                if (response != 'error') //si es diferente a error
                {
                  //convertimos a un objeto la respuesta y lo guardamos en la variable $info
                   var info = JSON.parse(response);
                   //console.log(info);

                   //LLAMAMOS A LA FUNCION generaPDF AD.1, recibiendo dos parametros
                   generarPDF(info.codcliente,info.nofactura);
                   //recargue la ventana de pdf
                   location.reload(); //refrescamos la pagina con reload   
                }else{
                  console.log('no data');
                }
              },
              error: function(error) {
              }

          });
      }
   });  

    //MODAL FORM ANULAR FACTURA
    $(".anular_factura").click(function(e){

        e.preventDefault(); 
        var nofactura = $(this).attr('fac');// attr permite accceder a los atributos de los elementos en el html
        var action = 'infoFactura';//esta extrayendo la informacion del producto AF.1

        //ajax
        $.ajax({
            url: 'ajax.php',
            type: 'POST',//se envia por el metodo post
            async: true, //ejecucion de manera asincrona
            data:{action:action,nofactura:nofactura},//datos que enviamos al archivo ajax.php`AG.1
      
            success: function(response){
            //si no hay error de la consulta del ajax.php
            if(response != 'error'){
                //convertimos  a un objeto el formato json para imprimir y acceder con javascrip
                var info = JSON.parse(response);

                //mostramos en pantalla

                 //FORMULARIO DE AJAX    en base a la funcion infoFactura(), anulamos el registro                                                   funcion anularFactura AG.1
                 $('.bodyModal').html('<form action="" method="post" name="form_anular_factura" id="form_anular_factura" onsubmit="event.preventDefault(); anularFactura();">'+
                                      '<h1> <i class="fas fa-cubes" style="font-size: 45pt;"></i> <br> Anular Factura</h1><br>'+//
                                      '<p>¿Realmente desea anular la factura?</p>'+

                                       '<p><strong>No. '+info.nofactura+'</strong></p>'+//accedemos a sus propiedades por medio del . info.nofactura  
                                       '<p><strong>Monto. Gs. '+info.totalfactura+'</strong></p>'+
                                       '<p><strong>Fecha/hora. '+info.fecha+'</strong></p>'+
                                       '<input type="hidden" name="action" value="anularFactura">'+//enviamos la funcion anularFactura AG.1
                                       '<input type="hidden" name="no_factura" id="no_factura" value="'+info.nofactura+'" required>'+//esto se envia a la funcion AH.1


                                      '<div class="alert alertAddProduct"></div>'+
                                      '<button type="submit" class="btn_ok"><i class="fas fa-trash-alt"></i> Anular</button>'+
                                      '<a href="#" class="btn_cancel" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>'+
                                   '</form>'); 

               }

             },

            //success: function(error){
              //console.log(error);   
            //} 
        });
       //muestra el id de c/u de los productos
        $('.modal').fadeIn(); 
    });

    //Ver factura //reimpresion
    $('.view_factura').click(function(e) {
      e.preventDefault();
      var codCliente = $(this).attr('cl');
      var noFactura = $(this).attr('f'); 

      //invocamos la funcion para generar factura pdf
      generarPDF(codCliente,noFactura);
    });

    //CAMBIAR PASSWORD
    $('.newPass').keyup(function(event) {
       //console.log($(this).val());//mostramos en consola lo que estamos escribiendo 
       //llamamos a la funcion AG.1
       validPass();

    });

    //form Cambiar contraseña = para el boton cambiar contraseña AH.1
    $('#frmChangePass').submit(function(e) {
        e.preventDefault();
        //alamcenamos los varlores de los campos en las variables
        var passActual = $('#txtPassUser').val();
        var passNuevo  = $('#txtNewPassUser').val();
        var confirmPassNuevo = $('#txtPassConfirm').val();
        //accion que enviamos por medio del ajax
        var action     = "changePassword"; //lo que enviamos al ajax AI.1

        //validacion de los campos en consola
        if (passNuevo != confirmPassNuevo){//validamos
            $('.alertChangePass').html('<p style="color:red;">Las contraseñas no son iguales.</p>');//si es diferente nos muestra la alerta en html en el parrafo
            $('.alertChangePass').slideDown();//con la funcion slideDown lo mostramos
            return false;//detiene el proceso
          }

          //colocamos un nivel de seguridad a las contraseñas
          if (passNuevo.length < 5){//si los caracteres cargados es menor a 6 caracteres
            $('.alertChangePass').html('<p style="color:red;">La nueva contraseña debe ser de 5 caracteres como mínimo.</p>');
            $('.alertChangePass').slideDown();//mostramos
            return false;
          }//si no se ejecuta las codiciones se ejecuta el ajax

          $.ajax({
            url: 'ajax.php',
            type: "POST",
            async : true,
            data: {action:action,passActual:passActual,passNuevo:passNuevo},

            success: function(response)
            {
              //console.log(response);
              //validamos que no nos devuelva un error AJ.1
              if (response != 'error') 
              {
                  var info = JSON.parse(response);//convertimos en objeto el json
                  //validamos los nros devueltos del ajax $code
                  if (info.cod == '00'){
                    //mostramos en el alert concatenados con el mensaje recibido del ajax info.msg 
                    $('.alertChangePass').html('<p style="color:green;">'+info.msg+'</p>');
                    $('#frmChangePass')[0].reset();//reseteamos los campos del formulario
                  }else{//sino mostramos el mensaje en html en el parrafo info.msg
                     $('.alertChangePass').html('<p style="color:red;">'+info.msg+'</p>') 
                  }
                  $('.alertChangePass').slideDown();//mostramos el div de alerta
              }
            },
            error: function(error)
            {

            }

          });    
    });

    //ACTUALIZAR DATOS DE LA EMPRESA frmEmpresa AK.1
    $('#frmEmpresa').submit(function(e) {
       e.preventDefault();
       //guardamos en las variables los datos de la empresa  capturados del los values de los campos html
       var intCI         = $('#txtCI').val();
       var strNombreEmp  = $('#txtNombre').val();
       var strRsocialEmp = $('#txtRSocial').val();
       var intTelEmp     = $('#txtTelEmpresa').val();
       var strEmailEmp   = $('#txtEmailEmpresa').val();
       var strDirEmp     = $('#txtDirEmpresa').val();
       var intIva        = $('#txtIva').val();

       //verificamos si las variables estan vacios
       if (intCI == '' || strNombreEmp == '' || intTelEmp == '' || strEmailEmp == '' || strDirEmp == '' || intIva == '') {
         $('.alertFormEmpresa').html('<p style="color:red">Todos los campos son obligatorios.</p>');//lo extraemos del html
         $('.alertFormEmpresa').slideDown();//mostramos el display alertFormEmpresa que esta en none en el html 
         return false;//detenemos el proceso si es que se aplica(si los campos estan vacios)
       }//si no se aplica se ejecuta el ajax

       $.ajax({
         url: 'ajax.php',
         type: 'POST',
         async: true,//la data va ser el formulario completo
         data: $('#frmEmpresa').serialize(),//serializa todos los datos, #frmEmpresa es el id 
         beforeSend: function(){//beforeSend= ejecuta lo de abajo mientras los datos se estan enviando
              $('.alertFormEmpresa').slideUp();//ocultamos el alert
              $('.alertFormEmpresa').html('');//dejamos vacios el div
              $('#frmEmpresa input').attr('disabled', 'disabled');//nos dirigimos al input y desactivamos mientras los datos se envian
         },

         success: function(response)
         {
              //console.log(response); //mostrar en consola
              var info = JSON.parse(response);//convertimos a objetos
              if (info.cod == '00') {//si es 00, se ejecuto correctamente
                $('.alertFormEmpresa').html('<p style="color: #23922d;">'+info.msg+'</p>');//concatenamos el mensaje de respuesta Ak.2
                $('.alertFormEmpresa').slideDown();//mostramos el alert
              }else{
                $('.alertFormEmpresa').html('<p style"color:red;">'+info.msg+'</p>');//concatenamos el mensaje de respuesta Ak.2
              }
              $('.alertFormEmpresa').slideDown();//mostramos el alert
              $('#frmEmpresa.input').removeAttr('disabled');//habilitamos nuevamente los campos
         },

         error: function(error){

         }
       });
  
   
     
       
    });




}); // End Ready

//FUNCION VALIDACION QUE LAS CONTRASEÑAS SEAN IGUALES AG.1
function validPass(){
    var passNuevo = $('#txtNewPassUser').val();//almacenamos en la variable  el valor que tiene el campo txtNewPassUser 
    var confirmPassNuevo = $('#txtPassConfirm').val();//almacenamos tambien lo que tiene el sgte campo
    if (passNuevo != confirmPassNuevo){//validamos
      $('.alertChangePass').html('<p style="color:red;">Las contraseñas no son iguales.</p>');//si es diferente nos muestra la alerta en html en el parrafo
      $('.alertChangePass').slideDown();//con la funcion slideDown lo mostramos
      return false;//detiene el proceso
    }

    //colocamos un nivel de seguridad a las contraseñas
    if (passNuevo.length < 5){//si los caracteres cargados es menor a 6 caracteres
      $('.alertChangePass').html('<p style="color:red;">La nueva contraseña debe ser de 5 caracteres como mínimo.</p>');
      $('.alertChangePass').slideDown();//mostramos
      return false;
    }
    //si no vaciamos los campos
     $('.alertChangePass').html('');
     $('.alertChangePass').slideDown();



}

function anularFactura(){//nombre de la funcion AG.1
  var noFactura = $('#no_factura').val();//obtenemos del input hidden el valor del elemento AH.1
  var action    = 'anularFactura';//AH.1

  $.ajax({
    url: 'ajax.php',
    type: 'POST',
    async: true,
    data: {action:action,noFactura:noFactura},//enviamos la informacion, el action y noFactura

    success: function(response)
    {
       //cerramos la ventana ajax y actualizamos el estado de la factura en el html
       if (response == 'error') {//si la respuesta es error
            $('.alertAddProduct').html('<p style="color:red;">Error al anular la factura.</p>');//error al anular la factura
       }else{//sino
            $('#row_'+noFactura+' .estado').html('<span class"anulada">Anulada</span>');//capturamos el id por medio del row, luego el estado AI.1
            $('#form_anular_factura .btn_ok').remove();//en el modal removemos el boton Anular
            //en el html colocamos el boton anular en inactivo
            $('#row_'+noFactura+' .div_factura').html('<button type="button" class="btn_anular inactive" ><i class="fas fa-ban"></i></button>');
            $('.alertAddProduct').html('<p>Factura anulada.</p>');//colocamos el texto factura anulada
       }

    },
    error: function(error) {
      /* Act on the event */
    }

  });
  
}


function generarPDF(cliente, factura){//recibe dos parametros, cliente y nro de facutra
  //MOSTRAR LA VENTANA DE PDF AD.1  
        var ancho = 1000;//ancho de la ventana a mostrar
        var alto  = 800;//alto de la ventana
        //Calcular posicion x,y para centrar la ventana
        var x = parseInt((window.screen.width/2) - (ancho / 2));//calculamos en el centro
        var y = parseInt((window.screen.height/2) - (alto / 2));
        //enviamos datos por medio del metodo GET por medio de la url
        $url = 'factura/generaFactura.php?cl='+cliente+'&f='+factura;//la $url se dirige a la sgte carpeta y recibe dos varibles, cl cliente y f factura
       //window.open va abrir la url cargada arriba, indicando la posicion de la ventana //resizable indica si se va ser grande o pequeña la ventana
        window.open($url,"Factura","left"+x+",top"+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");
}

//Funcion eliminar producto del detalle ventas tabla temporal
function del_product_detalle(correlativo){//recibe como parametro el correlativo

    var action = 'del_product_detalle';
    var id_detalle = correlativo; //select id_detalle,  where correlativo cargado

    $.ajax({
            url: 'ajax.php',
            type: 'POST',//se envia por el metodo post
            async: true, //ejecucion de manera asincrona
            data:{action:action,id_detalle:id_detalle},//datos que enviamos al archivo ajax.php`
      
            success: function(response) //validamos en AA.1
            {
              //console.log(response);//observamos en consola los datos que devuelve
              //
              //RECARGA LA PANTALLA DESPUES DE ELIMINAR UN PRODUCTO DEL DETALLE
              if (response != 'error')//evaluamos la respuesta que nos esta trayendo el ajax.php
                {
                  var info = JSON.parse(response);//convertimos en objeto el response obtenido a travez del ajax.php

                //manipulamos los objetos devueltos en la variable info
                $('#detalle_venta').html(info.detalle);//colocamos el id #detalle_venta(campo), la info.detalle rescatado del objeto
                $('#detalle_totales').html(info.totales);//colocamos el id #detalle_totales(campo), la info.detalle rescatado del objeto

                //luego limpiamos todos los campos- SETEAMOS
                $('#txt_cod_producto').val('');
                $('#txt_descripcion').html('-');
                $('#txt_existencia').html('-');
                $('#txt_cant_producto').val('0');
                $('#txt_precio').html('0.00');
                $('#txt_precio_total').html('0.00');

                //Bloqueamos el campo cantidad, seteamos
                $('#txt_cant_producto').attr('disabled', 'disabled');
                //Ocultamos la accion agregar
                $('#add_product_venta').slideUp();//con slideUp ocultamos
                
                }else{//limpiamos los campos detalle y el total
                  $('#detalle_venta').html('');
                  $('#detalle_totales').html('');


                }
                viewProcesar();//funcion mostrar/ocultar boton procesar AC 1

            },
            error: function(error) {
            }
        });

}

//mostrar u ocultar boton de procesar AC 1
function viewProcesar(){
  if ($('#detalle_venta tr').length > 0)//ingresa a detalle_venta y se dirige a las filas por medio del tr, si es mayor a 0 
  {
      $('#btn_facturar_venta').show();//muestra el boton procesar con .show
  }else{
      $('#btn_facturar_venta').hide();//de lo contrario lo oculta
  }
}

//Funcion para mantener la pagina 'nueva venta' cargada sin eliminar los detalles  al pasar a otra pestaña o refrescar Z.1
function serchForDetalle(id){//colocamos como parametro el id del usuario
    var action = 'serchForDetalle';
    var user = id; //el id usuario que recibimos como parametro

    $.ajax({
            url: 'ajax.php',
            type: 'POST',//se envia por el metodo post
            async: true, //ejecucion de manera asincrona
            data:{action:action,user:user},//datos que enviamos al archivo ajax.php`
      
            success: function(response)
            {
              //console.log(response);//observamos en consola los datos que devuelve
              if (response != 'error') //si el listado que obtenemos del ajax.php es distino a error ref Y.1
              {
                var info = JSON.parse(response);//convertimos el formato json a un objeto, otra vez
                //manipulamos los objetos devueltos en la variable info
                $('#detalle_venta').html(info.detalle);//colocamos el id #detalle_venta(campo), la info.detalle rescatado del objeto
                $('#detalle_totales').html(info.totales);//colocamos el id #detalle_totales(campo), la info.detalle rescatado del objeto
 
              }else{
                console.log('no hay datos');
              }
              viewProcesar();//funcion mostrar/ocultar boton procesar
            },
            error: function(error) {
            }
        });



}
//funcion url = retorna direccion url donde se encuentra nuestro proyecto
function getUrl(){
  var loc = window.location;
  var pathName = loc.pathname.substring(0,loc.pathname.lastIndexOf('/') + 1);
  return loc.href.substring(0, loc.href.length -((loc.pathname + loc.search + loc.hash).length - pathName.length));

}


//funcion para eliminar los productos k.1 
function delProduct(){
    //obtenemos del html el id del producto almacenando en var pr
    var pr = $('#producto_id').val() //M.1
    //alert("Enviar datos");
    //cada vez que cerramos limpiamos
      $('.alertAddProduct').html('');

       $.ajax({
            url: 'ajax.php',
            type: 'POST',//se envia por el metodo post
            async: true,
            data:$('#form_del_product').serialize(),// enviaron todos datos a ajax.php por medio del id del formulario`
      
            success: function(response){
            console.log(response);//observamos los datos que devuelve
            //si contiene error
             if(response == 'error')//L.1
              { 
                //devolvemos una mensaje de alerta dentro del formulario ajax
                $('.alertAddProduct').html('<p style="color: red;">Error al eliminar el producto.</p>');//B.1
              }else{ 
                 //retornamos los datos concatenados, accedemos a la fila y lo removemos
                 $('.row'+pr).remove();//M.1 concatenamos el pr para remover
                 //removemos el boton eliminar; accedemos al formulario por medio del id del formulario 
                 $('#form_del_product .btn_ok').remove();//N.1
                 //mostramos un mensaje de alerta
                 $('.alertAddProduct').html('<p>Producto eliminado correctamente.</p>');
              }
             },

             error: function(error){
                console.log(error);    
             } 
        });
}


function sendDataProduct(){
    //alert("Enviar datos");
    //cada vez que cerramos limpiamos
      $('.alertAddProduct').html('');

       $.ajax({
            url: 'ajax.php',
            type: 'POST',//se envia por el metodo post
            async: true,
            data:$('#form_add_product').serialize(),// enviaron todos datos a ajax.php por medio del id del formulario`
      
            success: function(response){
            console.log(response);//observamos los datos que devuelve
            //si contiene error
              if(response == 'error')//A.1
              { 
                //devolvemos una mensaje de alerta en el formulario
                $('.alertAddProduct').html('<p style="color: red; ">Error al agregar el producto.</p>');//B.1
              }else{
                //sino, devolvemos el array y convertimos en formato json
                 var info = JSON.parse(response);
                 //retornamos los datos concatenados y seteamos los valores
                 $('.row'+info.producto_id+' .celPrecio').html(info.nuevo_precio);//C.1
                 $('.row'+info.producto_id+' .celExistencia').html(info.nueva_existencia);//C.2
                 //limpiamos los txt de la ventanita ajax
                 $('#txtCantidad').val('');
                 $('#txtPrecio').val('');
                 //mostramos un mensaje de alerta
                 $('.alertAddProduct').html('<p>Producto guardado correctamente.</p>');

              }

             },

             error: function(error){
                console.log(error);    
             } 
        });

}

//boton cerrar formulario //ventanita, limpiamos la ventana al cerrar
function closeModal(){
    $('#txtCantidad').val('');//limpiamos los campos
     $('#txtPrecio').val('');
    $('.modal').fadeOut();

}
