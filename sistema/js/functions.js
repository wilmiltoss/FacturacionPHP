

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

    $('.delPhoto').click(function(){
    	$('#foto').val('');
    	$(".delPhoto").addClass('notBlock');
    	$("#img").remove();

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
});


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
                 $('#txtExistencia').val('');
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
