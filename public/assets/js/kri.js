	$("#uni_med").change(function() {

		if ($("#uni_med").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{
				//mostramos cotas y agregamos atributos max a cotas
				$("#cotas").fadeIn(500);
				$("#green_min").attr('max','100');
				$("#interval_max").attr('max','100');
				$("#interval_min").attr('max','100');
				$("#red_max").attr('max','100');
			}

			else if ($("#uni_med").val() == 1) //monto
			{
				//mostramos cotas y agregamos atributos max a cotas
				$("#cotas").fadeIn(500);
				$("#green_min").removeAttr('max');
				$("#interval_max").removeAttr('max');
				$("#interval_min").removeAttr('max');
				$("#red_max").removeAttr('max');
			}

			else if ($("#uni_med").val() == 2) //cantidad
			{
				//mostramos cotas y agregamos atributos max a cotas
				$("#cotas").fadeIn(500);
				$("#green_min").removeAttr('max');
				$("#interval_max").removeAttr('max');
				$("#interval_min").removeAttr('max');
				$("#red_max").removeAttr('max');
			}
		}

		else
		{
			$("#green_min").val('');
			$("#interval_min").val('');
			$("#description_green").val('');
			$("#interval_max").val('');
			$("#description_yellow").val('');
			$("#red_max").val('');
			$("#description_red").val('');
			$("#cotas").fadeOut(500);
		}
	});

//Varias funciones de cambio para poner mensaje
	$("#green_min").change(function() {
		tramos_distintos();
		if ($("#green_min").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{	
				if ($("#green_min").val() > 100)
				{
					$("#div_green_min").attr('class','form-group has-error has-feedback');
					$("#error_min_green").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
					$("#guardar").attr('disabled','true');
				}
				else
				{
					$("#div_green_min").attr('class','form-group');
					$("#error_min_green").empty();
					$("#guardar").removeAttr('disabled');
				}
			}
		}
	});

	$("#interval_min").change(function() {
		tramos_distintos();
		if ($("#interval_min").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{	
				if ($("#interval_min").val() > 100)
				{
					$("#div_interval_min").attr('class','form-group has-error has-feedback');
					$("#error_interval_min").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
					$("#guardar").attr('disabled','true');
				}
				else
				{
					$("#div_interval_min").attr('class','form-group');
					$("#error_interval_min").empty();
					$("#guardar").removeAttr('disabled');
				}
			}
		}
	});

	$("#interval_max").change(function() {
		tramos_distintos();
		if ($("#interval_max").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{	
				if ($("#interval_max").val() > 100)
				{
					$("#div_interval_max").attr('class','form-group has-error has-feedback');
					$("#error_interval_max").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
					$("#guardar").attr('disabled','true');
				}
				else
				{
					$("#div_interval_max").attr('class','form-group');
					$("#error_interval_max").empty();
					$("#guardar").removeAttr('disabled');
				}
			}
		}
	});

	$("#red_max").change(function() {
		tramos_distintos();
		if ($("#red_max").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{	
				if ($("#red_max").val() > 100)
				{
					$("#div_red_max").attr('class','form-group has-error has-feedback');
					$("#error_max_red").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
					$("#guardar").attr('disabled','true');
				}
				else
				{
					$("#div_red_max").attr('class','form-group');
					$("#error_max_red").empty();
					$("#guardar").removeAttr('disabled');
				}
			}
		}
	});


	$("#uni_med").change(function() {
		if ($("#uni_med").val() == 0) //porcentaje
		{	
			//si es que se cambia la unidad de medida, se debe volver a verificar para cada una de las cotas los valores (en caso de porcentaje)
			//verde
			if ($("#green_min").val() > 100)
			{
				$("#div_green_min").attr('class','form-group has-error has-feedback');
				$("#error_min_green").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
				$("#guardar").attr('disabled','true');
			}
			else
			{
				$("#div_green_min").attr('class','form-group');
				$("#error_min_green").empty();
				$("#guardar").removeAttr('disabled');
			}

			if ($("#interval_min").val() > 100)
			{
				$("#div_interval_min").attr('class','form-group has-error has-feedback');
				$("#error_interval_min").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
				$("#guardar").attr('disabled','true');
			}
			else
			{
				$("#div_interval_max").attr('class','form-group');
				$("#error_interval_max").empty();
				$("#guardar").removeAttr('disabled');
			}
			
			if ($("#interval_max").val() > 100)
			{
				$("#div_interval_max").attr('class','form-group has-error has-feedback');
				$("#error_interval_max").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
				$("#guardar").attr('disabled','true');
			}
			else
			{
				$("#div_interval_max").attr('class','form-group');
				$("#error_interval_max").empty();
				$("#guardar").removeAttr('disabled');
			}

			if ($("#red_max").val() > 100)
			{
				$("#div_red_max").attr('class','form-group has-error has-feedback');
				$("#error_max_red").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
				$("#guardar").attr('disabled','true');
			}
			else
			{
				$("#div_red_max").attr('class','form-group');
				$("#error_max_red").empty();
				$("#guardar").removeAttr('disabled');
			}
		}
		//volvemos a normalidad todas las cotas
		else
		{
			$("#div_green_min").attr('class','form-group');
			$("#error_min_green").empty();
			$("#div_interval_min").attr('class','form-group');
			$("#error_interval_min").empty();

			$("#div_interval_max").attr('class','form-group');
			$("#error_interval_max").empty();

			$("#div_red_max").attr('class','form-group');
			$("#error_max_red").empty();
			$("#guardar").removeAttr('disabled');
		}
	});

function menorMayor(elem1,elem2) //función de comparación para función js sort de menor a mayor
{
	return elem1 - elem2; 
}
function mayorMenor(elem1,elem2) //función de comparación para función js sort de mayor a menor
{
	return elem2 - elem1; 
}

function changeVals()
{
	$("#green_min").change();
	$("#interval_min").change();
	$("#interval_max").change();
	$("#red_max").change();
}
//función para ordenar valores
function ordenamiento()
{
	//guardamos en array los cuatro valores a ordenar
	var array = [];
	var new_array = [];
	array.push($("#green_min").val(),$("#interval_min").val(),$("#interval_max").val(),$("#red_max").val());
	//alert(array);
	//alert($('input[name=min_max]:checked').val());
	if ($('input[name=min_max]:checked').val() == 1) //mayor a menor
	{
		new_array = array.sort(mayorMenor);
		//alert(new_array);

		//seteamos nuevos valores
		$("#green_min").val(new_array[0]);
		$("#interval_min").val(new_array[1]);
		$("#interval_max").val(new_array[2]);
		$("#red_max").val(new_array[3]);

		changeVals();

	}

	else if ($('input[name=min_max]:checked').val() == 2) //menor a mayor
	{
		new_array = array.sort(menorMayor);
		//alert(new_array);

		//seteamos nuevos valores
		$("#green_min").val(new_array[0]);
		$("#interval_min").val(new_array[1]);
		$("#interval_max").val(new_array[2]);
		$("#red_max").val(new_array[3]);

		changeVals();

		
	}
	
}

function tramos_distintos() //verifica que no haya intervalos iguales
{
	if ($("#green_min").val() == $("#interval_min").val() || $("#interval_min").val() == $("#interval_max").val() || $("#interval_max").val() == $("#red_max").val())
	{
		swal('Cuidado','Los intervalos deben ser distintos','error');
		$("#guardar").attr('disabled','true');
	}
}
