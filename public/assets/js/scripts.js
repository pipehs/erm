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

//función para eliminar datos
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
    var today = new Date();
    var date2= new Date(date);
        
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