// JavaScript Validation 

$('document').ready(function()
                    { 		 		
    // name validation
    var nameregex = /^[a-zA-Z ]+$/;
    var nameregex22 = /^[0-90-9 \.]+$/;

    $.validator.addMethod("validname", function( value, element ) {
        return this.optional( element ) || nameregex.test( value );
    }); 

    $.validator.addMethod("validnum", function( value, element ) {
        return this.optional( element ) || nameregex22.test( value );
    }); 

    // valid email pattern
    var eregex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    $.validator.addMethod("validemail", function( value, element ) {
        return this.optional( element ) || eregex.test( value );
    });

    $("#pago-form").validate({

        rules:
        {
            billingFirstName: {
                required: true,
                minlength: 4,
                maxlength: 30
            },

            billingLastName: {
                required: true,
                minlength: 4,
                maxlength: 30
            },
            
            
            billingCity: {
                required: true,
            },
            
            billingAddress: {
                required: true,
            },
cp: {
                required: true,
            },


            

            shippingReceiverIdentifier: {
                required: true,
                validnum:true
            },
             reserved4: {
                required: true,
               

            }, 
            
             reserved5: {
                required: true,

            }, 

          

            billingPhoneNumber: {
                required: true,
                validnum: true
            },


           
            billingEmail: {
                required: true,
                validemail: true
            },
            purchaseAmount: {
                required: true,
                validnum:true,
                maxlength: 11,
            },
            additionalObservations: {
                maxlength: 50
            },
            
            billingPostalCode: {
                required: true,
            },
            
        },
        messages:
        {

            billingFirstName: {
                required: "Ingrese su nombre por favor!",
                minlength: "Tu nombre es demasiado corto(Minimo 4 letras)",
                maxlength: "Tu nombre es demasiado largo(Maximo 30 letras)"
            },

            billingLastName: {
                required: "Ingrese su apellido por favor!",
                minlength: "Tu nombre es demasiado corto(Minimo 4 letras)",
                maxlength: "Tu nombre es demasiado largo(Maximo 30 letras)"
            },

           
            shippingReceiverIdentifier: {
                required: "Por favor introducir un documento ",
                validnum: "Solo debe contener solo numeros"
            },
            
            billingGender: {
                required: "Por favor seleccione un sexo ",
            },
            reserved4: {
                required: "Por favor introducir un concepto de pago "
            },
            
            reserved5: {
                required: "Por favor introducir la referencia a pagar "          },

            
            billingPhoneNumber: {
                required: "Por favor introducir su Numero de telefono",
                validnum: "Solo debe contener  numeros"
            },
billingAddress:{
 required: "Por favor introducir su direccion"  
 },
 
 
 
billingCity:{
 required: "Por favor introducir su ciudad"  
 },
 
 billingPostalCode:{
 required: "Por favor introducir su codigo postal"  
 },
 
           
            billingEmail: {
                required: "Por favor escribir su correo electronico",
                validemail: "Escribir un correo electronico Valido (nombre@dominio.com)",
            },
            purchaseAmount:{
                required: "por favor escribir el monto a pagar",
                validnum: "por favor escribir solo numero ",
                maxlength: "el monto a pagar  es demasiado larga (maximo 10 numero)"

            },
            additionalObservations:{
                maxlength: "Tu observacion es demasiado largo(Maximo 50 digitos)"
            }
        },
        errorPlacement : function(error, element) {
            $(element).closest('.form-group').find('.help-block').html(error.html());
        },
        highlight : function(element) {
            $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
            $(element).closest('.form-group').find('.help-block').html('');
        },

        submitHandler: function(form){

            form.submit();
            //var url = $('#register-form').attr('action');
            //location.href=url;

        }

        /*submitHandler: function() 
							   { 
							   		alert("Submitted!");
									$("#register-form").resetForm(); 
							   }*/

    }); 


    /*function submitForm(){


			   /*$('#message').slideDown(200, function(){

				   $('#message').delay(3000).slideUp(100);
				   $("#register-form")[0].reset();
				   $(element).closest('.form-group').find("error").removeClass("has-success");

			   });

			   alert('form submitted...');
			   $("#register-form").resetForm();

		   }*/
});