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
        var producto = $(this).attr('product');
        var action = 'infoProducto';

        //ajax
        $.ajax({
            url: 'ajax.php',
            type: 'POST',//se envia por el metodo post
            async: true,
            data:{action:action,producto:producto},//datos que enviamos al archivo ajax.php`
      
            success: function(response){
            console.log(response);//observamos los datos que devuelve

            if(response != 'error'){
                //convertimos en ob jeto el formato json para imprimir y acceder con javascrip
                var info = JSON.parse(JSON.stringify(response));
                 var info = JSON.parse(response);
                //var info = jQuery.parseJSON(response);

                console.log(info);//imprimimos el info

                //mostramos en pantalla
                 $('#producto_id').val(info.codproducto);
                 $('.nameProducto').html(info.descripcion);

               }

             },

        success: function(error){
            console.log(error);    
             } 
        });

       //muestra el id de c/u de los productos
        $('.modal').fadeIn(); 
    });

});
//boton cerrar formulario //ventanita
function closeModal(){
    $('.modal').fadeOut();

}
