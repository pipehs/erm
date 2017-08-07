// Run Datables plugin and create 3 variants of settings
function AllTables(){
	TestTable1();
	TestTable2();
	TestTable3();
	LoadSelect2Script(MakeSelect2);
}

function MakeSelect2(){
	$('select').select2();
	$('.dataTables_filter').each(function(){
		$(this).find('label input[type=text]').attr('placeholder', 'Search');
	});
}
// Run Select2 on element
function Select2Test(){
	$("#el2").select2();
	$("#el3").select2();
}

//función para bloquear (primeramente para datos maestros)
function bloquear(id,name,kind,type)
{
	swal({   title: "Atención!",
		   text: "Esta seguro de bloquear "+type+" "+name+"?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Bloquear",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get(kind+'.bloquear.'+id, function (result) {
		   			swal({   title: "",
		   			   text: ""+type+" "+name+" fue bloqueado(a) con éxito ",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false }, 
		   			   function(){   
		   			   	location.reload();
		   			   });

		   			});
		   		 
		   	});
}

//función para desbloquear (primeramente para datos maestros)
function desbloquear(id,name,kind,type)
{
	swal({   title: "Atención!",
		   text: "Esta seguro de desbloquear "+type+" "+name+"?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Desbloquear",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get(kind+'.desbloquear.'+id, function (result) {
		   			swal({   title: "",
		   			   text: ""+type+" "+name+" fue bloqueado(a) con éxito ",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false }, 
		   			   function(){   
		   			   	location.reload();
		   			   });

		   			});
		   		 
		   	});
}

//función para cerrar plan de auditoría (se dejará los atributos kind y type para el caso en que se necesite)
function closer(id,name,kind,type)
{
	swal({   title: "Atención!",
		   text: "Está seguro que desea cerrar el "+type+" "+name+"?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Cerrar",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get(kind+'.close.'+id, function (result) {
		   			swal({   title: "",
		   			   text: ""+type+" "+name+" fue cerrado exitosamente ",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false }, 
		   			   function(){   
		   			   	location.reload();
		   			   });

		   			});
		   		 
		   	});
}

//función para abrir plan de auditoría (se dejará los atributos kind y type para el caso en que se necesite)
function opening(id,name,kind,type)
{
	swal({   title: "Atención!",
		   text: "Está seguro que desea abrir el "+type+" "+name+"?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Abrir",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get(kind+'.open.'+id, function (result) {
		   			swal({   title: "",
		   			   text: ""+type+" "+name+" fue reabierto exitosamente ",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false }, 
		   			   function(){   
		   			   	location.reload();
		   			   });

		   			});
		   		 
		   	});
}

//función para eliminar datos (sustituye antigua funcion eliminar)
function eliminar2(id,name,kind,type)
{
	swal({   title: "Warning!",
		   text: "Está seguro de eliminar "+type+" "+name+"?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Eliminar",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get(kind+'.destroy.'+id, function (result) {
		   			if (result == 0)
		   			{
		   				swal({   title: "",
			   			   text: ""+type+" "+name+" fue eliminado(a) satisfactoriamente",
			   			   type: "success",   
			   			   showCancelButton: false,   
			   			   confirmButtonColor: "#31B404",   
			   			   confirmButtonText: "Aceptar",   
			   			   closeOnConfirm: false }, 
			   			   function(){   
			   			   	location.reload();
			   			});
		   			}
		   			else
		   			{
		   				swal({   title: "",
			   			   text: ""+type+" "+name+" no puede ser eliminado(a). Posiblemente contenga información asociada.",
			   			   type: "error",   
			   			   showCancelButton: false,   
			   			   confirmButtonColor: "#31B404",   
			   			   confirmButtonText: "Aceptar",   
			   			   closeOnConfirm: false }, 
			   			   function(){   
			   			   	location.reload();
			   			});
		   			}

		   		});	 
		   });
}

//función para validar un KPI
function validatekpi(id,name)
{
	swal({   title: "Atención!",
		   text: "Esta seguro de validar la última medición del KPI "+name+"?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Validar",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get('kpi.validate.'+id, function (result) {
		   			swal({   title: "",
		   			   text: "La medición del KPI "+name+" fue validada con éxito ",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false }, 
		   			   function(){   
		   			   	location.reload();
		   			   });

		   			});
		   		 
		   	});
}

//función para eliminar datos (primeramente solo hallazgos)
function eliminar(id,name,kind,type)
{
	swal({   title: "Atención!",
		   text: "Esta seguro de eliminar "+type+" "+name+"?. Se borrarán todos sus datos asociados",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Eliminar",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get('delete_'+kind+'.'+id, function (result) {
		   			swal({   title: "",
		   			   text: ""+type+" "+name+" fue eliminado(a) con éxito ",
		   			   type: "success",   
		   			   showCancelButton: false,
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false }, 
		   			   function(){   
		   			   	location.reload();
		   			   });

		   			});
		   	});
	
}

//función para eliminar evidencias
function eliminar_ev(id,kind,name)
{
	swal({   title: "Atención!",
		   text: "Esta seguro de eliminar este documento?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#FF0000",   
		   confirmButtonText: "Eliminar",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get('evidences.delete.'+id+','+kind+','+name, function (result) {
		   			swal({   title: "",
		   			   text: "El documento fue eliminado con éxito ",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false }, 
		   			   function(){   
		   			   	location.reload();
		   			  });
		   		});
		   		 
		   	});
	//confirm("Esta seguro de bloquear "+type+" "+name+"?")
}

function validarFechaMayorActual(date)
{
	//por error de js (que está restando un día a la fecha ingresada); modificaremos ésta para parsear como INT (y agregar hora)
	var date_temp = date.split('-');
    var today = new Date();
    var date2 = new Date(date_temp[0]+'-'+date_temp[1]+'-'+parseInt(date_temp[2])+' 23:59:59');

    //Actualización 02-11-2016: Agregamos validador de fecha menor a 31-12-9999
    var date3 = new Date('9999-12-'+parseInt(31)+' 23:59:59');

    if (date2 > date3)
    {
    	swal('Error!','Está ingresando una fecha incorrecta. La fecha mayor a ingresar es 31-12-9999','error');
        $("#exp_date").prop('class','form-group has-error has-feedback');
        $("#submit").prop('disabled',true);
    }

    else if (date2 < today)
    {   
        swal('Cuidado!','Está ingresando una fecha menor a la fecha actual','warning');
        $("#exp_date").prop('class','form-group has-error has-feedback');
        $("#submit").prop('disabled',true);
    }
    else
    {
        $("#exp_date").prop('class','form-group');
        $("#submit").prop('disabled',false);
    }   
}

//compara 2 fechas y verifica que una sea menor a la otra (en el caso de plan de auditoría)
function compararFechas(fecha_menor,fecha_mayor)
{
	//primero hacemos la misma validación de arriba
    var date_temp1 = fecha_menor.split('-');
    var date_temp2 = fecha_mayor.split('-');
    var today = new Date();
    var date1 = new Date(date_temp1[0]+'-'+date_temp1[1]+'-'+parseInt(date_temp1[2])+' 23:59:59');
    var date2 = new Date(date_temp2[0]+'-'+date_temp2[1]+'-'+parseInt(date_temp2[2])+' 23:59:59');
        
    if (date2<today)
    {   
        swal('Cuidado!','Está ingresando una fecha menor a la fecha actual','warning');
        $("#init_date").attr('class','form-group has-error has-feedback');
    }
    else if (date1<today)
    {
    	swal('Cuidado!','Está ingresando una fecha menor a la fecha actual','warning');
        $("#fin_date").attr('class','form-group has-error has-feedback');
    }
    else
    {
		if (fecha_mayor != "" && fecha_menor != "")
		{
			// ----------------- REALIZAR LA COMPARACIÓN -----------------//
			if (fecha_menor >= fecha_mayor)
			{
				swal('Cuidado!','La fecha de inicio debe ser menor a la fecha de término','error');
				$("#init_date").attr('class','form-group has-error has-feedback');
				$("#fin_date").attr('class','form-group has-error has-feedback');
			}
			else
			{
				$("#init_date").attr('class','form-group');
				$("#fin_date").attr('class','form-group');
			}
		}
	}
}

//función para cerrar evaluación de prueba de control
function cerrar_evaluacion(id,name,kind)
{
	swal({   title: "Atención!",
		   text: "Está seguro que desea cerrar "+kind+" para el control "+name+"?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Cerrar",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get('cerrar_evaluacion.'+id, function (result) {
		   			swal({   title: "",
		   			   text: ""+kind+" del control "+name+" fue cerrada exitosamente ",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false }, 
		   			   function(){   
		   			   	location.reload();
		   			   });

		   			});
		   		 
		   	});
}

//funciones de expandir y contraer para descripción en index de distintos elementos
function expandir(id,description,short_des)
{
	description2 = "'"+description+"'";
	short_des2 = "'"+short_des+"'";

	$('#description_'+id).html(description+'<div style="cursor:hand" onclick="contraer('+id+','+description2+','+short_des2+')"><font color="CornflowerBlue">Ocultar</font></div>')
}

function contraer(id, description,short_des)
{
	description2 = "'"+description+"'";
	short_des2 = "'"+short_des+"'";

	$('#description_'+id).html(short_des+'... <div style="cursor:hand" onclick="expandir('+id+','+description2+','+short_des2+')"><font color="CornflowerBlue">Ver completo</font></div>')
}

//funciones expandir y contraer para recomendaciones de hallazgos
function expandir2(id,description,short_des)
{
	description2 = "'"+description+"'";
	short_des2 = "'"+short_des+"'";

	$('#recommendation_'+id).html(description+'<div style="cursor:hand" onclick="contraer2('+id+','+description2+','+short_des2+')"><font color="CornflowerBlue">Ocultar</font></div>')
}

function contraer2(id, description,short_des)
{
	description2 = "'"+description+"'";
	short_des2 = "'"+short_des+"'";

	$('#recommendation_'+id).html(short_des+'... <div style="cursor:hand" onclick="expandir2('+id+','+description2+','+short_des2+')"><font color="CornflowerBlue">Ver completo</font></div>')
}

//funciones expandir y contraer para recomendaciones de planes de acción
function expandir3(id,description,short_des)
{
	description2 = "'"+description+"'";
	short_des2 = "'"+short_des+"'";

	$('#action_plan_'+id).html(description+'<div style="cursor:hand" onclick="contraer3('+id+','+description2+','+short_des2+')"><font color="CornflowerBlue">Ocultar</font></div>')
}

function contraer3(id, description,short_des)
{
	description2 = "'"+description+"'";
	short_des2 = "'"+short_des+"'";

	$('#action_plan_'+id).html(short_des+'... <div style="cursor:hand" onclick="expandir3('+id+','+description2+','+short_des2+')"><font color="CornflowerBlue">Ver completo</font></div>')
}

//AGREGADO 02-08-17
function checkSubmit() {
    document.getElementById("btnsubmit").value = "Enviando...";
    document.getElementById("btnsubmit").disabled = true;
    return true;
}


$(document).ready(function() {
	// Load Datatables and run plugin on tables 
	LoadDataTablesScripts(AllTables);
	// Load script of Select2 and run this
	LoadSelect2Script(Select2Test);
	// Add slider for change test input length
	FormLayoutExampleInputLength($( ".slider-style" ));
	// Initialize datepicker
	$('#input_date').datepicker({setDate: new Date()});
	// Initialize datepicker 2
	$('#input_date2').datepicker({setDate: new Date()});
	// Load Timepicker plugin
	//LoadTimePickerScript(DemoTimePicker);
	// Add tooltip to form-controls
	$('.form-control').tooltip();
	// Load example of form validation
	LoadBootstrapValidatorScript(DemoFormValidator);
	// Add Drag-n-Drop feature
	WinMove();

	$( "first-disabled option:first-child").attr("disabled", "disabled");
});

