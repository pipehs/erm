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

//función para bloquear o desbloquear (primeramente para datos maestros)
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
function eliminar_ev(id,kind)
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
		   		$.get('evidences.delete.'+id+','+kind, function (result) {
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

    if (date2<today)
    {   
        swal('Cuidado!','Está ingresando una fecha menor a la fecha actual','warning');
        $("#exp_date").attr('class','form-group has-error has-feedback');
    }
    else
    {
        $("#exp_date").attr('class','form-group');
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