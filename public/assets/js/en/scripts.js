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
	swal({   title: "Warning!",
		   text: "Are you sure to block "+type+" "+name+"?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Block",
		   cancelButtonText: "Cancel",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get(kind+'.bloquear.'+id, function (result) {
		   			swal({   title: "",
		   			   text: ""+type+" "+name+" was blocked successfully ",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Accept",   
		   			   closeOnConfirm: false }, 
		   			   function(){   
		   			   	location.reload();
		   			   });

		   			});
		   		 
		   	});
}

//función para bloquear o desbloquear (primeramente para datos maestros)
function eliminar2(id,name,kind,type)
{
	swal({   title: "Warning!",
		   text: "Are you sure to delete "+type+" "+name+"?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Delete",
		   cancelButtonText: "Cancel",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get(kind+'.destroy.'+id, function (result) {
		   			if (result == 0)
		   			{
		   				swal({   title: "",
			   			   text: ""+type+" "+name+" was successfully deleted ",
			   			   type: "success",   
			   			   showCancelButton: false,   
			   			   confirmButtonColor: "#31B404",   
			   			   confirmButtonText: "Accept",   
			   			   closeOnConfirm: false }, 
			   			   function(){   
			   			   	location.reload();
			   			});
		   			}
		   			else
		   			{
		   				swal({   title: "",
			   			   text: ""+type+" "+name+" could not be deleted. Perhaps it have associated data.",
			   			   type: "error",   
			   			   showCancelButton: false,   
			   			   confirmButtonColor: "#31B404",   
			   			   confirmButtonText: "Accept",   
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
	swal({   title: "Warning!",
		   text: "Are you sure to validate the last measurement of KPI "+name+"?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Validar",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get('kpi.validate.'+id, function (result) {
		   			swal({   title: "",
		   			   text: "The measurement of KPI "+name+" was successfully validated ",
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

//eliminar issue (por ahora será distinto a la funcion eliminar2 simplemente por el texto del mensaje)
function eliminar(id,name,kind,type)
{
	swal({   title: "Atención!",
		   text: "Are you sure to delete "+type+" "+name+"?. All associated data will be deleted",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Eliminar",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get('delete_'+kind+'.'+id, function (result) {
		   			swal({   title: "",
		   			   text: ""+type+" "+name+" was deleted successfully",
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
	swal({   title: "Warning!",
		   text: "Are you sure of delete this document?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#FF0000",   
		   confirmButtonText: "Eliminar",
		   cancelButtonText: "Cancelar",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get('evidences.delete.'+id+','+kind, function (result) {
		   			swal({   title: "",
		   			   text: "The document was deleted successfully",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Accept",   
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
    var today = new Date();
    var date2= new Date(date);
        
    if (date2<today)
    {   
        swal('Warning!','You entered a date lower than current date','warning');
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
	var today = new Date();
    var date1 = new Date(fecha_mayor);
    var date2 = new Date(fecha_menor);
        
    if (date2<today)
    {   
        swal('Warning!','You entered a date lower than current date','warning');
        $("#init_date").attr('class','form-group has-error has-feedback');
    }
    else if (date1<today)
    {
    	swal('Cuidado!','You entered a date lower than current date','warning');
        $("#fin_date").attr('class','form-group has-error has-feedback');
    }
    else
    {
		if (fecha_mayor != "" && fecha_menor != "")
		{
			// ----------------- REALIZAR LA COMPARACIÓN -----------------//
			if (fecha_menor >= fecha_mayor)
			{
				swal('Warning!','Initial date must be lower than final date','error');
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
	LoadTimePickerScript(DemoTimePicker);
	// Add tooltip to form-controls
	$('.form-control').tooltip();
	// Load example of form validation
	LoadBootstrapValidatorScript(DemoFormValidator);
	// Add Drag-n-Drop feature
	WinMove();

	$( "first-disabled option:first-child").attr("disabled", "disabled");

	

});