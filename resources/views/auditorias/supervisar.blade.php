@extends('master')

@section('title', 'Auditor&iacute;as - Supervisi&oacute;n de auditor&iacute;as')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="auditorias">Auditor&iacute;as</a></li>
			<li><a href="supervisar">Supervisi&oacute;n de auditor&iacute;as</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Supervisi&oacute;n de auditor&iacute;as</span>
				</div>
				<div class="box-icons">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
					<a class="expand-link">
						<i class="fa fa-expand"></i>
					</a>
					<a class="close-link">
						<i class="fa fa-times"></i>
					</a>
				</div>
				<div class="move"></div>
			</div>
			<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">
	      	<p>En esta secci&oacute;n podr&aacute; supervisar los pruebas de auditor&iacute;a generadas anteriormente.</p>

				@if(Session::has('message'))
					<div class="alert alert-success alert-dismissible" role="alert">
					{{ Session::get('message') }}
					</div>
				@endif
				@if(Session::has('error'))
					<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('error') }}
					</div>
				@endif

				<div id="cargando"><br></div>

				{!!Form::open(['route'=>'agregar_supervision','method'=>'POST','class'=>'form-horizontal','id'=>'form',
				'enctype'=>'multipart/form-data'])!!}
	      			@include('auditorias.form_basico_audit')

					<div id="audit_programs"></div>
					
				{!!Form::close()!!}

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>


$("#audit").change(function() {
			if ($("#audit").val() != '') //Si es que se ha seleccionado valor válido de plan
			{
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="/bgrcdemo2/assets/img/loading.gif" width="19" height="19"/></center></div>');
				//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
					//primero obtenemos controles asociados a los riesgos de negocio

					//obtenemos pruebas relacionadas a la auditoría seleccionada
					$.get('auditorias.get_audit_program2.'+$("#audit").val(), function (result) {

							$("#cargando").html('<br>');
							$("#audit_programs").empty();

							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							var cont = 1; //contador de pruebas
							tests_id = []; //array con id de pruebas para guardar en PHP 
							programs_id = []; //array con id de programas para guardar en PHP
							//seteamos datos en select de auditorías
							$(datos).each( function() {
								programs_id.push(this.id);

								//por cada prueba del programa
								$(this.audit_tests).each( function(i,test) {
									tests_id.push(test.id)
									var audit_test = '<h4><b>' + test.name +'</b></h4>';
									audit_test += '<b>Descripci&oacute;n: '+test.description+'</b><br>';

									if (test.stakeholder == null)
									{
										audit_test += '<b>Responsable: No definido</b></br>';
									}
									else
									{
										audit_test += '<b>Responsable: '+test.stakeholder+'</b><br>';
									}

									if (test.status == 0)
									{
										audit_test += '<b>Estado: Abierta</b><br>';
									}
									else if (test.status == 1)
									{
										audit_test += '<b>Estado: En ejecución</b><br>';
									}
									else if (test.status == 2)
									{
										audit_test += '<b>Estado: Cerrada</b><br>';	
									}
									
									if (test.results == 0)
									{
										audit_test += '<b>Resultado: Inefectiva</b><br>';
									}
									else if (test.results == 1)
									{
										audit_test += '<b>Resultado: Efectiva</b><br>';
									}
									else if (test.results == 2)
									{
										audit_test += '<b>Resultado: En proceso</b><br>';	
									}

									if (test.files == 'undefined' || test.files == null || test.files == '')
									{
										filestemp = null
									}
									else
									{
										files = "'"+test.files+"'";
										//dividimos files por la coma
										filestemp = files.split(',')
										audit_test += '<b>Documentos cargados:</b> ';

									}
									$(filestemp).each( function(i,file) {

										filetemp = file.split('.')
										filename = filetemp[0].split('/')
										filename = filename[2]


										if (filetemp[1] == 'jpg' || filetemp[1] == 'jpeg' || filetemp[1] == 'JPG')
										{
											audit_test += '<a href="../storage/app/'+file+'" download><img src="assets/img/jpg.png" width="30" height="30" />'+filename+'</a>'
										}
										else if (filetemp[1] == 'pdf')
										{
											audit_test += '<a href="../storage/app/'+file+'" download><img src="assets/img/pdf.png" width="30" height="30" />'+filename+'</a>'
										}
										else if (filetemp[1] == 'doc' || filetemp[1] == 'docx')
										{
											audit_test += '<a href="../storage/app/'+file+'" download><img src="assets/img/word.png" width="30" height="30" />'+filename+'</a>'
										}
										else if (filetemp[1] == 'xls' || filetemp[1] == 'xlsx')
										{
											audit_test += '<a href="../storage/app/'+file+'" download><img src="assets/img/excel.png" width="30" height="30" />'+filename+'</a>'
										}
										else if (filetemp[1] == 'ppt' || filetemp[1] == 'pptx')
										{
											audit_test += '<a href="../storage/app/'+file+'" download><img src="assets/img/powerpoint.png" width="30" height="30" />'+filename+'</a>'
										}
										else if (filetemp[1] == 'png' || filetemp[1] == 'PNG')
										{
											audit_test += '<a href="../storage/app/'+file+'" download><img src="assets/img/png.png" width="30" height="30" />'+filename+'</a>'
										}
										else
										{
											audit_test += '<a href="../storage/app/'+file+'" download><img src="assets/img/desconocido.png" width="30" height="30" />'+filename+'</a>'
										}

									});

									audit_test += '</br>';
									audit_test += '<b>Hallazgos encontrados</b><hr>';
									cont = 1; //contador de hallazgos
									$(test.issues).each( function(i,issue) {
										//alert(issue.name);
										audit_test += '<ul><b>Hallazgo '+cont+'</b>'

										if (issue.classification == 0)
										{
											audit_test += '<li>Clasificación: Oportunidad de mejora</li>';
										}
										else if (issue.classification == 1)
										{
											audit_test += '<li>Clasificación: Deficiencia</li>';
										}
										else if (issue.classification == 2)
										{	
											audit_test += '<li>Clasificación: Debilidad significativa</li>';
										}
										audit_test += '<li>Nombre: '+issue.name+'</li>';

										if (issue.description == '' || issue.description == null)
										{
											audit_test += '<li>Descripción: No se ha agregado descripción</li>';
										}
										else
										{
											audit_test += '<li>Descripción: '+issue.description+'</li>';
										}

										if (issue.recommendations == '' || issue.recommendations == null)
										{
											audit_test += '<li>Recomendaciones: No se han agregado recomendaciones</li></ul><hr>';
										}
										else
										{
											audit_test += '<li>Recomendaciones: '+issue.recommendations+'</li></ul><hr>';
										}

									});

									audit_test += '<div id="nota_cerrada_'+test.id+'"></div>';
									audit_test += '<div style="cursor:hand" id="btn_notas_'+test.id+'" onclick="notas('+test.id+')" class="btn btn-success">Notas</div><hr> ';

									$("#audit_programs").append(audit_test);

									$("#audit_programs").append('<div id="notas_'+test.id+'" style="display: none;"></div>');

								});

							});
	
					});

			}
			else
			{
				$("#audit").empty();
			}

});

function evidencias(id)
{
	alert("Hola evidencias "+id);
}

function ocultar_notas(id)
{
	$("#notas_"+id).hide(500);
}

//crea una nota para la prueba de audit_audit_plan_audit_test_id = id
function crear_nota(id)
{
	$("#crear_nota_"+id).empty();
	$("#crear_nota_"+id).append('<div style="cursor:hand" id="crear_nota_'+id+'" onclick="ocultar_notas('+id+')">Ocultar</div>');
	//vaciamos por si existe ya algún formulario
	$("#nueva_nota_"+id).empty();
	var nota = '<div class="form-group col-sm-12">';
	//agregamos atributo hidden que señalará que se está guardando una nota y otro para identificar el id de la prueba
	nota += '<input type="hidden" name="test_id" value="'+id+'">';
	nota += '<input type="hidden" name="type" value="0">'; //0 identifica nueva nota;
	nota += '<input type="text" name="name_'+id+'" class="form-control" placeholder="Nombre de la nota" required></div>';
	nota += '<div class="form-group col-sm-12">';
	nota += '<textarea name="description_'+id+'" rows="3" cols="4" class="form-control" placeholder="Nota" required></textarea></div>';
	nota += '<div class="form-group col-sm-12">';
	nota += '<select name="stakeholder_id" class="form-control">'
	nota += '<option value="" disabled selected>- Seleccione a quien irá dirigida la nota -</option>';
	//seteamos datos en select de auditorías
	$(stakeholders).each( function() {
			nota += '<option value="' + this.rut + '">' + this.fullname +'</option>';
	});
	nota += '´</select>';
	nota += '<div class="form-group col-sm-12">';
	nota += '<label class="control-label">Cargar evidencias (opcional)</label>';

	nota += '<input id="file-1" type="file" class="file" name="evidencia_'+id+'[]" multiple=true data-preview-file-type="any">';
	nota += '<div class="form-group col-sm-12">';
	nota += '<button class="btn btn-success">Guardar</button></div><hr><br>';
	$("#nueva_nota_"+id).append(nota);
	$("#nueva_nota_"+id).show(500);

}

function cerrar_nota(id,note_id)
{
	$.get('auditorias.close_note.'+id, function (result) {
		//parseamos datos obtenidos
		var datos = JSON.parse(result);

		if (datos == 1) //la nota se cerro
		{
			$("#notas_"+note_id).hide(500);

			var res = '<div class="alert alert-success alert-dismissible" role="alert">'
			res += 'Nota cerrada exitosamente';
			$("#nota_cerrada_"+note_id).append(res);

			//movemos pantalla a mensaje de nota cerrada
			$('html,body').animate({
			    scrollTop: $("#nota_cerrada_"+note_id).offset().top -100
			}, 1000);
		}
	});
}

</script>
{!!Html::script('assets/js/audits.js')!!}
{!!Html::script('assets/js/notas.js')!!}
{!!Html::script('assets/js/descargar.js')!!}
@stop
