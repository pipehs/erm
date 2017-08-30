@extends('master')

@section('title', 'Ayuda')

@section('content')

<style>
body {
	text-align: justify;
}

#manual {
	font-size: 16px;
}

</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('help','Ayuda')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Ayuda</span>
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
				<div class="no-move"></div>
			</div>
			<div class="box-content">
			@if(Session::has('message'))
				<div class="alert alert-danger alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			{!!Form::open(['route'=>'pdf_manual','method'=>'POST','class'=>'form-horizontal'])!!}
				{!!Form::hidden('cuerpo',null,['id' => 'cuerpo'])!!}
				{!!Form::submit("Exportar a PDF",['class'=>'btn btn-danger'])!!}
			{!!Form::close()!!}
			{!! link_to_route('pdf_manual', $title = 'Exportar a PDF', $parameters = NULL, $attributes = ['class'=>'btn btn-danger'])!!}

		<div id="manual">
			<p>En esta secci&oacute;n podr&aacute; visualizar todas las funciones correspondientes al sistema (haga click sobre las im&aacute;genes para poder verlas en tamaño completo).</p>

			<div class="menu" id="menu">
				<ul style="list-style: none;">
				 	<li><a id="masterdatamenu" style="color: #66a2d0;" href="#"><strong>Datos Maestros</strong></a></li>
				 	<li><a id="strategymenu" style="color: #66a2d0;" href="#"><strong>Gestión Estratégica</strong></a></li>
				 	<li><a id="risksmenu" style="color: #66a2d0;" href="#"><strong>Gestión de Riesgos</strong></a></li>
				 	<li><a id="controlsmenu" style="color: #66a2d0;" href="#"><strong>Gestión de Controles</strong></a></li>
				 	<li><a id="auditsmenu" style="color: #66a2d0;" href="#"><strong>Gestión de Auditorías</strong></a></li>
				 	<li><a id="issuesmenu" style="color: #66a2d0;" href="#"><strong>Mantenedor de Hallazgos</strong></a></li>
				 	<li><a id="action_plans_menu" style="color: #66a2d0;" href="#"><strong>Mantenedor de Planes de Acción</strong></a></li>
				 	<li><a id="reportsmenu" style="color: #66a2d0;" href="#"><strong>Reportes Básicos</strong></a></li>
				 	<li><a id="docsmenu" style="color: #66a2d0;" href="#"><strong>Gestor de Documentos</strong></a></li>
				</ul>
			</div>
			<br><br>
			<h4 id="masterdata"><strong>Datos Maestros</strong>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a id="volvermenu" style="color: #66a2d0; font-size: 10px;" href="#">Volver a Menú</a></h4>
			<hr>
			<p>En esta sección se puede gestionar todos los datos maestros que serán utilizados por las distintas funcionalidades del sistema.<br/>
			A través de cada uno de estos módulos podrá ver 

			{!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!},
			 crear {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!},
			  modificar {!! HTML::image('assets/img/Manual/3.PNG',"Imagen no encontrada",['title'=>'3','width'=>'20', 'height'=>'20']) !!} o 
			  bloquear {!! HTML::image('assets/img/Manual/4.PNG',"Imagen no encontrada",['title'=>'4','width'=>'20', 'height'=>'20']) !!} cada uno de los datos maestros (ver Figura 1).</p>

			  <center>
			  <a data-fancybox="gallery" href="assets/img/Manual/imagen5.png">{!! HTML::image('assets/img/Manual/imagen5.png',"Imagen no encontrada",['title'=>'Figura 1','width'=>'200', 'height'=>'150']) !!}</a></center>
			  <p style="font-size: 11px; text-align: center;">Figura 1.</p>

			<p>A través del botón {!! HTML::image('assets/img/Manual/5.PNG',"Imagen no encontrada",['title'=>'5','width'=>'20', 'height'=>'20']) !!}, accederá a una sección donde podrá observar todos los datos maestros que han sido bloqueados. Al bloquear uno de estos datos, éste no podrá enlazarse a los distintos elementos del sistema, sin embargo, podrá ser desbloqueado o eliminado completamente a través de la sección mencionada.<br/><br/>

			Al ingresar a “Agregar elemento” deberá completar un formulario con todos los datos relevantes a la misma, como puede observar en la Figura 2 (en general los únicos datos obligatorios son Nombre y Descripción).</p>

			<center>
			<a data-fancybox="gallery" href="assets/img/Manual/imagen6.png">{!! HTML::image('assets/img/Manual/imagen6.png',"Imagen no encontrada",['title'=>'Figura 2','width'=>'200', 'height'=>'150']) !!}</a>
			  <p style="font-size: 11px; ">Figura 2.</p></center>

			<p>Es importante mencionar que los procesos para cada uno de los datos maestros son similares, por lo que se usó la sub sección de organizaciones a modo de ejemplo. Por otra parte, también es importante mencionar que cada uno de los datos maestros podrá ser eliminado <b>sólo si</b> no posee un elemento enlazado. A modo de ejemplo, en la Figura 3 se puede observar que el sistema no permite que el proceso de ejemplo “Proceso 2” sea eliminado, ya que éste posee subprocesos asociados.</p>

			<center>
			<a data-fancybox="gallery" href="assets/img/Manual/imagen7.png">{!! HTML::image('assets/img/Manual/imagen7.png',"Imagen no encontrada",['title'=>'Figura 3','width'=>'200', 'height'=>'150']) !!}</a>
			  <p style="font-size: 11px;">Figura 3. Denegación para eliminar dato maestro con elementos asociados.</p>
			</center>

			<h4 id="strategy"><strong>Gestión Estratégica</strong>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a id="volvermenu" style="color: #66a2d0; font-size: 10px;" href="#">Volver a Menú</a></h4>
			<hr>

			<p>A través de esta sección podrá ver, editar y generar planes estratégicos para su organización, además de definir los objetivos estratégicos enmarcados en dicho plan. Además, podrá visualizar el mapa generado para su plan estratégico vigente, y gestionar los KPI (Key Performance Indicator).</p>

			<ol>
				<li><b>Planes y objetivos estratégicos</b><br>
				<p>En esta sección deberá ingresar primero que todo la organización a la cual desea gestionar sus planes y objetivos estratégicos. Una vez seleccionada la organización, accederá a la ventana principal de planes estratégicos, donde podrá visualizar todos los planes generados para su organización {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!}, generar nuevos planes {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!}, editar los existentes {!! HTML::image('assets/img/Manual/3.PNG',"Imagen no encontrada",['title'=>'3','width'=>'20', 'height'=>'20']) !!}, además de poder verlos objetivos corporativos asociados a cada uno de los planes estratégicos {!! HTML::image('assets/img/Manual/4.PNG',"Imagen no encontrada",['title'=>'4','width'=>'20', 'height'=>'20']) !!} (ver Figura 4). Es importante destacar que solamente se podrán gestionar los objetivos corporativos para el plan estratégico de la organización que se encuentre vigente.</p>
				</li>
			

				<center>
				<a data-fancybox="gallery" href="assets/img/Manual/imagen8.png">{!! HTML::image('assets/img/Manual/imagen8.png',"Imagen no encontrada",['title'=>'Figura 4','width'=>'200', 'height'=>'150']) !!}</a>
				  <p style="font-size: 11px;">Figura 4. Planes y objetivos estratégicos.</p>
				</center>

				<li><b>Mapa estratégico</b>
				<p>En esta sección podrá visualizar el mapa estratégico de su organización para su plan estratégico vigente. Es importante mencionar que, para poder observar el mapa estratégico de su organización, es necesario que primero defina un plan estratégico con sus respectivos objetivos corporativos.</p>
				</li>

				<li><b>Monitor KPI</b>
				<p>En esta sección podrá gestionar los indicadores claves de rendimiento para su organización. Para esto, primero que todo deberá seleccionar la organización. En caso de que no exista ningún KPI generado previamente, se mostrará una ventana como la que se puede observar en la Figura 5.</p>

				<center>
				<a data-fancybox="gallery" href="assets/img/Manual/imagen9.png">{!! HTML::image('assets/img/Manual/imagen9.png',"Imagen no encontrada",['title'=>'Figura 5','width'=>'200', 'height'=>'150']) !!}</a>
				  <p style="font-size: 11px;">Figura 5. No se han agregado KPI a la organización.</p>
				</center><br>

				Haciendo click en el botón {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!} mostrado en la Figura 5, podrá acceder al formulario para crear un nuevo KPI. Los datos a completar son los siguientes:
					<ul>
						<li>-	Objetivos involucrados: Objetivos que serán medidos a través del KPI</li>
						<li>-	Nombre: Nombre del KPI</li>
						<li>-	Descripción: Descripción del KPI</li>
						<li>-	Forma de cálculo: Texto que describe la forma en que será calculado el KPI</li>
						<li>-	Periodicidad: Cada cuanto será evaluado el KPI</li>
						<li>-	Responsable: Encargado del KPI</li>
						<li>-	Valor inicial: Valor numérico inicial y tipo de este valor (Pesos, Dólares, Porcentaje, Cantidad).</li>
						<li>-	Fecha Inicio: Cuando comienza a regir los periodos de evaluación del KPI</li>
						<li>-	Fecha Término: Fecha final del KPI</li>
						<li>-	Meta del KPI: Valor numérico que define el monto que se desea alcanzar a través del KPI</li>
					</ul>

				<p>Al momento de generar KPI’s, éstos podrán ser visualizados en la sección principal del Monitor de KPI ordenados según las perspectivas de objetivos que están midiendo, como se puede observar en la Figura 6.</p>

				<center>
				<a data-fancybox="gallery" href="assets/img/Manual/imagen10.png">{!! HTML::image('assets/img/Manual/imagen10.png',"Imagen no encontrada",['title'=>'Figura 6','width'=>'200', 'height'=>'150']) !!}</a>
				  <p style="font-size: 11px;">Figura 6. Sección principal monitor de KPI.</p>
				</center><br>

				<p>Para poder visualizar el valor inicial ingresado, tanto en la tabla como en el gráfico del KPI, se debe validar dicho valor. Es importante destacar que cada vez que se realice una medición de KPI, el valor ingresado debe ser validado a través del botón correspondiente (Validar). En su defecto, si se desea modificar el último valor ingresado, se podrá realizar haciendo click en el botón de Medir, donde también podrá observar el gráfico de mediciones, como se puede observar en la Figura 7.</p>

				<center>
				<a data-fancybox="gallery" href="assets/img/Manual/imagen11.png">{!! HTML::image('assets/img/Manual/imagen11.png',"Imagen no encontrada",['title'=>'Figura 7','width'=>'200', 'height'=>'150']) !!}</a>
				  <p style="font-size: 11px;">Figura 7. Medición e información histórica de KPI seleccionado.</p>
				</center><br>
				</li>
			</ol>

			<h4 id="risks"><strong>Gestión de Riesgos</strong>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a id="volvermenu" style="color: #66a2d0; font-size: 10px;" href="#">Volver a Menú</a></h4>
			<hr>

			<ol>
				<li><b>Eventos de Riesgo</b>

				<p>A través de esta sección se podrán crear, ver, editar, eliminar o revisar encuestas de evaluación para identificar posibles eventos de riesgo.</p>

				<ol>
					<li><u>Crear Encuesta</u>
					<p>Para crear una encuesta debe ingresar el nombre de la misma, la cantidad de preguntas que desea incluir y un texto para cada una de estas preguntas. Luego, para cada una de estas preguntas, deberá escoger el tipo de respuesta que desea en éstas, como se puede observar en la Figura 8.</p>

					<center>
					<a data-fancybox="gallery" href="assets/img/Manual/imagen12.png">{!! HTML::image('assets/img/Manual/imagen12.png',"Imagen no encontrada",['title'=>'Figura 8','width'=>'200', 'height'=>'150']) !!}</a>
					  <p style="font-size: 11px;">Figura 8. Creación de encuestas de eventos de riesgos.</p>
					</center><br>

					<p>Los tipos de respuesta posibles son:<br>
						<ul>
						<li>- Texto: La persona que responda puede ingresar un texto libre.</li>
						<li>- Radio Button: La persona que responda puede seleccionar una (y sólo una) respuesta correcta.</li>
						<li>- Checkbox: La persona que responda puede seleccionar múltiples respuestas correctas.</li>
						</ul><br>
						Una vez ingresados los tipos de respuesta, la encuesta ya se encuentra agregada y lista para ser enviada, como se puede ver en la Figura 9.</p>

						<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen13.png">{!! HTML::image('assets/img/Manual/imagen13.png',"Imagen no encontrada",['title'=>'Figura 9','width'=>'200', 'height'=>'150']) !!}</a>
						  <p style="font-size: 11px;">Figura 9. Encuesta agregada con éxito.</p>
						</center><br>
					</li>
					<li><u>Enviar Encuesta</u>

					<p>Para realizar el envío de una encuesta previamente creada, debe seleccionar la encuesta y el método de envío. El método de envío puede ser seleccionar usuarios manualmente, enviar a todos los usuarios de una organización, o enviar por un tipo de usuario (por ejemplo, enviar a todos los Auditores).</p>

					<p>Luego, aparecerá una ventana similar a la mostrada en la Figura 10, donde en {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!} podrá seleccionar usuarios, roles u organizaciones (según corresponda al método escogido), en {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!} podrá personalizar el mensaje que será enviado por correo a los usuarios, y en {!! HTML::image('assets/img/Manual/3.PNG',"Imagen no encontrada",['title'=>'3','width'=>'20', 'height'=>'20']) !!} podrá ver la encuesta seleccionada.</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen14.png">{!! HTML::image('assets/img/Manual/imagen14.png',"Imagen no encontrada",['title'=>'Figura 10','width'=>'200', 'height'=>'150']) !!}</a>
						  <p style="font-size: 11px;">Figura 10. Envío de encuesta de eventos de Riesgos.</p>
					</center><br>
					</li>

					<li><u>Revisi&oacute;n de Encuestas</u>

					<p>Una vez enviada, la encuesta, en la sección Revisión de Encuestas podrá revisar los usuarios a los que ha sido enviada la encuesta y podrá revisar las respuestas que estos usuarios han brindado.</p>
					</li>
				</ol>

				<li><b>Identificación de Riesgos</b>

				<p>A través de esta sección se pueden generar riesgos previamente identificados. En la ventana principal, luego de seleccionar la organización y una categoría de riesgo (opcionalmente), podrá seleccionar si desea agregar un riesgo de proceso {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!} o un riesgo de negocio {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!}. Además, podrá ver la lista de Riesgos que han sido identificados para la organización seleccionada {!! HTML::image('assets/img/Manual/3.PNG',"Imagen no encontrada",['title'=>'3','width'=>'20', 'height'=>'20']) !!}.</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen15.png">{!! HTML::image('assets/img/Manual/imagen15.png',"Imagen no encontrada",['title'=>'Figura 11','width'=>'200', 'height'=>'150']) !!}</a>
						  <p style="font-size: 11px;">Figura 11. Identificación de Riesgos.</p>
					</center><br>
				</li>

				<p>Los datos que se deben ingresar para identificar un riesgo son:</p>

				<ul>
					<li>- Subproceso(s) u Objetivo(s) involucrado(s)*: Como paso fundamental se debe seleccionar a lo menos un subproceso u objetivo que esté siendo afectado por el riesgo.</li>
					<li>- Riesgo tipo: Opcionalmente, se puede generar un riesgo basándose en una plantilla creada previamente (creada a través de la sección de Riesgos tipo en datos maestros).</li>
					<li>- Nombre*: Nombre del riesgo.</li>
					<li>- Descripción: Texto que describe el riesgo.</li>
					<li>- Categoría: Categoría definida previamente a la cual el riesgo pertenece.</li>
					<li>- Responsable: Encargado del riesgo.</li>
					<li>- Fecha expiración: Fecha en que el riesgo debería dejar de ser válido.</li>
					<li>- Pérdida esperada: Pérdida económica esperada si el riesgo llega a materializarse.</li>
					<li>- Causa(s): Posible causa o causas que pueden provocar la materialización del riesgo, definidas previamente en datos maestros, o definidas en esta misma sección haciendo click en el botón de Agregar Nueva Causa.</li>
					<li>- Efecto(s): Posible efecto o efectos de la materialización del riesgo, definidos previamente en datos maestros o definidos en esta sección haciendo click en el botón de Agregar Nuevo Efecto. </li>
				</ul>

				</li>
				<li><b>Evaluación de Riesgos</b>
				<p>En esta sección se podrán generar encuestas de evaluación de Riesgos, o evaluar manualmente los riesgos. A través de estas evaluaciones, se podrá medir cual es el impacto y probabilidad del riesgo inherentemente, es decir, sin considerar aun la posible mitigación del Riesgo por parte de los Controles internos.</p>

					<ol>
					<li><u>Crear Encuesta</u>
					<p>Para la creación de una encuesta de evaluación se deben ingresar todos los campos que se aparecen en la Figura 12 (el único campo opcional es la fecha de expiración).</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen16.png">{!! HTML::image('assets/img/Manual/imagen16.png',"Imagen no encontrada",['title'=>'Figura 12','width'=>'200', 'height'=>'150']) !!}</a>
						  <p style="font-size: 11px;">Figura 12. Creación de encuesta de evaluación de Riesgos.</p>
					</center><br>
					</li>

					<li><u>Ver Encuestas</u>
					<p>Una vez generada la encuesta de evaluación, se podrán aplicar distintas funcionalidades en la misma a través de la sub-sección de Encuestas agregadas en la sección de Evaluación de Riesgos (ver, enviar, consolidar, eliminar), como se puede observar en la Figura 13.</p>
					
					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen17.PNG">{!! HTML::image('assets/img/Manual/imagen17.PNG',"Imagen no encontrada",['title'=>'Figura 13','width'=>'200', 'height'=>'150']) !!}</a>
						  <p style="font-size: 11px;">Figura 13. Menú para encuestas agregadas.</p>
					</center><br>	
					</li>
						<ul>
							<li>- Ver: A través de esta opción podrá revisar los principales datos de la encuesta generada (Descripción de la encuesta, fecha de creación, expiración, y riesgos asociados). También podrá observar las respuestas entregadas por los usuarios que han respondido la encuesta.</li>
							<li>- Enviar: Esta funcionalidad permite enviar la encuesta seleccionando los usuarios que desee sean los destinatarios. Además, podrá personalizar el mensaje que será enviado por correo electrónico.</li>
							<li>- Consolidar: Una vez que la encuesta haya sido respondida por uno o varios usuarios a los que se envió, en la sección Consolidar se mostrará los promedios de las respuestas enviadas, como se puede observar en la Figura 14.

							<center>
								<a data-fancybox="gallery" href="assets/img/Manual/imagen18.PNG">{!! HTML::image('assets/img/Manual/imagen18.PNG',"Imagen no encontrada",['title'=>'Figura 14','width'=>'200', 'height'=>'150']) !!}</a>
								  <p style="font-size: 11px;">Figura 14. Consolidación de encuestas de riesgos.</p>
							</center><br>

							Luego, el usuario encargado de riesgos (RM) podrá validar estos valores, confirmándolos o modificándolos. Una vez consolidados, estos valores no podrán ser modificados para la encuesta y el valor asignado a los riesgos será almacenado y podrá ser utilizado en distintos reportes, como por ejemplo en el Mapa de Riesgos.	
							</li>			
						</ul>
					</li>
				</ol>
				<li><b>KRI</b>
				<p>A través de esta sección podrá crear y gestionar indicadores de riesgos clave (KRI), los cuales se pueden definir como un indicador para identificar si la probabilidad de que un riesgo ocurra junto a su impacto, superan el apetito de riesgo definido por la organización.</p>

					<ol>
						<li><u>Monitor KRI</u>

						<p>Para crear un nuevo KRI debe ingresar a la sub sección Monitor KRI, y luego hacer click en el botón Agregar Nuevo KRI. La información que se debe ingresar al agregar un nuevo KRI es la siguiente:</p>

						<ul>
							<li>- Riesgo: Identifica el riesgo de negocio que será medido a través del KRI. También se puede seleccionar un riesgo de proceso que haya sido previamente enlazado a un riesgo de negocio (a través de la sub-sección Vincular Riesgos).</li>
							<li>- Nombre: Nombre del KRI.</li>
							<li>- Descripción: Descripción del KRI.</li>
							<li>- Tipo: Identifica si el KRI será aplicado de forma manual o automática.</li>
							<li>- Periodicidad: Cada cuanto tiempo deberá se deberá realizar la medición del KRI.</li>
							<li>- Unidad de medida: Identifica si el KRI será medido a través de un porcentaje, un monto o una cantidad numérica.</li>
							<li>- Intervalos de KRI: Como se aprecia en la Figura 15, se debe seleccionar primero que todo, si el valor numérico máximo del KRI será identificado por el color Rojo o Verde. Por ejemplo, si un KRI busca medir la cantidad de ventas o utilidades generadas por una empresa en cierto periodo de tiempo, el valor numérico máximo será identificado por el color verde (ya que a mayor venta o mayores utilidades más positiva es la evaluación del KRI). Por el contrario, si el KRI busca medir la tasa de accidentes de una empresa en un periodo de tiempo, el valor numérico máximo deberá ser representado por el color rojo.

							<center>
								<a data-fancybox="gallery" href="assets/img/Manual/imagen19.PNG">{!! HTML::image('assets/img/Manual/imagen19.PNG',"Imagen no encontrada",['title'=>'Figura 15','width'=>'200', 'height'=>'150']) !!}</a>
								  <p style="font-size: 11px;">Figura 15. Valores de intervalos dentro de un KRI.</p>
							</center><br>
							</li>
							<li>- Descripción verde-amarillo-rojo: Describe que es lo que representa cada uno de los colores del KRI.</li>
						</ul>

						<p>Una vez generado el KRI, este podrá ser observado a través del monitor KRI y se podrán realizar distintas acciones sobre él (editar, evaluar, monitorear, eliminar), como se puede observar en la Figura 16.</p>

							<center>
								<a data-fancybox="gallery" href="assets/img/Manual/imagen20.PNG">{!! HTML::image('assets/img/Manual/imagen20.PNG',"Imagen no encontrada",['title'=>'Figura 16','width'=>'200', 'height'=>'150']) !!}</a>
								  <p style="font-size: 11px;">Figura 16.Monitor KRI.</p>
							</center><br>

						<ul>
						<li>- Editar: Se puede editar los principales datos del KRI ingresados en su creación.</li>
						<li>- Evaluar: Al evaluar un KRI, se puede observar el valor mínimo y máximo de la evaluación. Para realizar la evaluación, se debe ingresar un intervalo para ésta, y el valor de la evaluación en {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!} y {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!} mostrados en la Figura 17. 

							<center>
								<a data-fancybox="gallery" href="assets/img/Manual/imagen21.PNG">{!! HTML::image('assets/img/Manual/imagen21.PNG',"Imagen no encontrada",['title'=>'Figura 17','width'=>'200', 'height'=>'150']) !!}</a>
								  <p style="font-size: 11px;">Figura 17. Evaluar KRI.</p>
							</center><br>

						Además, haciendo click en {!! HTML::image('assets/img/Manual/3.PNG',"Imagen no encontrada",['title'=>'3','width'=>'20', 'height'=>'20']) !!} se pueden observar las evaluaciones anteriores, como se observa en la Figura 18.

							<center>
								<a data-fancybox="gallery" href="assets/img/Manual/imagen22.PNG">{!! HTML::image('assets/img/Manual/imagen22.PNG',"Imagen no encontrada",['title'=>'Figura 18','width'=>'200', 'height'=>'150']) !!}</a>
								  <p style="font-size: 11px;">Figura 18. Evaluaciones anteriores KRI.</p>
							</center><br>
						</li>
						<li>- Monitorear: A través de esta funcionalidad, podrá visualizar una tabla con todas las mediciones del KRI, además de un gráfico en el que se puede observar la evolución de éste, como se puede apreciar en la Figura 19.

							<center>
								<a data-fancybox="gallery" href="assets/img/Manual/imagen23.PNG">{!! HTML::image('assets/img/Manual/imagen23.PNG',"Imagen no encontrada",['title'=>'Figura 19','width'=>'200', 'height'=>'150']) !!}</a>
								  <p style="font-size: 11px;">Figura 19. Monitor de KRI.</p>
							</center><br>
						</li>
						<li>- Eliminar: Permite eliminar el KRI sólo en caso de que no existan evaluaciones asociadas a éste.</li>
						</ul>
					</li>
					<li><u>Riesgo - KRI</u>
					
					<p>Esta funcionalidad permite realizar las mismas funciones que el Monitor KRI, pero filtrando los KRI por el Riesgo que está siendo medido por el mismo.</p>
					</li>
					<li><u>Vincular Riesgo</u>
					<p>Esta función permite vincular un riesgo de proceso a un riesgo de negocio, con el fin de poder generar un KRI para dicho riesgo de proceso que directa o indirectamente, pueda influir en la ocurrencia del riesgo de negocio.</p>
					</li>

				</li>
			</ol>
			<br>
			<h4 id="controls"><strong>Gestión de Controles</strong>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a id="volvermenu" style="color: #66a2d0; font-size: 10px;" href="#">Volver a Menú</a></h4>
			<hr>

			<ol>
				<li><b>Mantenedor de controles</b>
				<p>A través del mantenedor de controles, podrá generar, ver, editar o eliminar controles para cualquier organización, tanto de procesos como de negocios. Primero que todo, deberá seleccionar la organización para la cual desea gestionar los controles. Luego (y en caso de existir), podrá observar todos los controles generados para la organización seleccionada, como se puede observar en la Figura 20.</p>

				<center>
					<a data-fancybox="gallery" href="assets/img/Manual/imagen24.PNG">{!! HTML::image('assets/img/Manual/imagen24.PNG',"Imagen no encontrada",['title'=>'Figura 20','width'=>'200', 'height'=>'150']) !!}</a>
					<p style="font-size: 11px;">Figura 20. Mantenedor de Controles.</p>
				</center><br>
				</li>

				<li><b>Evaluación de controles</b>
				<p>Esta funcionalidad permite evaluar los controles a través de pruebas de diseño, efectividad operativa, sustantivas y de cumplimiento. Primero que todo, deberá seleccionar la organización y el tipo de control (de proceso o de negocio). En caso de ser control de proceso, deberá seleccionar proceso y subproceso, para luego seleccionar el control que se desea evaluar. Por el contrario, si desea evaluar un control de negocio o de entidad, deberá seleccionar directamente el control.</p>
				
				<p>Al seleccionar, verá una ventana como la representada en la Figura 25. En {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!} podrá observar la información del control. Por otra parte, en {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!} podrá visualizar los resultados anteriores de los distintos tipos de prueba, además de generar nuevas evaluaciones, o en el caso de que se encuentre una evaluación abierta podrá editar ésta en {!! HTML::image('assets/img/Manual/3.PNG',"Imagen no encontrada",['title'=>'3','width'=>'20', 'height'=>'20']) !!} o cerrar la prueba (y guardar su resultado para la evaluación del riesgo controlado) en {!! HTML::image('assets/img/Manual/4.PNG',"Imagen no encontrada",['title'=>'4','width'=>'20', 'height'=>'20']) !!}.</p>	
				
				<center>
					<a data-fancybox="gallery" href="assets/img/Manual/imagen25.PNG">{!! HTML::image('assets/img/Manual/imagen25.PNG',"Imagen no encontrada",['title'=>'Figura 21','width'=>'200', 'height'=>'150']) !!}</a>
					<p style="font-size: 11px;">Figura 21. Evaluación de Controles.</p>
				</center><br>
				</li>

				<p>Al seleccionar una Nueva evaluación, deberá ingresar una descripción de la prueba que se realizó, y el resultado de ésta (efectivo o inefectivo), como se aprecia en la Figura 22.</p>

				<center>
					<a data-fancybox="gallery" href="assets/img/Manual/imagen26.PNG">{!! HTML::image('assets/img/Manual/imagen26.PNG',"Imagen no encontrada",['title'=>'Figura 22','width'=>'200', 'height'=>'150']) !!}</a>
					<p style="font-size: 11px;">Figura 22. Nueva evaluación de Control.</p>
				</center><br>

				<p>En el caso de que la prueba sea efectiva, podrá ingresar comentarios asociados a estos resultados. Por otra parte, si el resultado es inefectivo, podrá gestionar hallazgos asociados a esta prueba (para lo cual primero debe guardar el resultado de la prueba).</p>
			</ol>
			<br>
			<h4 id="audits"><strong>Gestión de Auditorías</strong>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a id="volvermenu" style="color: #66a2d0; font-size: 10px;" href="#">Volver a Menú</a></h4>
			<hr>

			<ol>
			<li><b>Planes de auditoría</b><br>
			<p>En la sección de planes de auditoría, podrá crear y gestionar todos los planes de auditoría generados en el sistema. En la ventana principal de esta sección (ver Figura 23), se puede ver la lista de planes de auditoría generados, además de poder crear un nuevo plan {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!}, cerrar un plan generado {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!}, ver un plan de auditoría {!! HTML::image('assets/img/Manual/3.PNG',"Imagen no encontrada",['title'=>'3','width'=>'20', 'height'=>'20']) !!} o eliminar dicho plan {!! HTML::image('assets/img/Manual/4.PNG',"Imagen no encontrada",['title'=>'4','width'=>'20', 'height'=>'20']) !!}, siempre que no tenga información asociada.</p>

				<center>
					<a data-fancybox="gallery" href="assets/img/Manual/imagen27.PNG">{!! HTML::image('assets/img/Manual/imagen27.PNG',"Imagen no encontrada",['title'=>'Figura 23','width'=>'200', 'height'=>'150']) !!}</a>
					<p style="font-size: 11px;">Figura 23. Ventana principal de planes de auditoría.</p>
				</center><br>

			<p>Al hacer click en Ver, podrá observar toda la información asociada al plan de auditoría, además de tener la opción de editar dicho plan, haciendo click en el botón Editar (ver Figura 24), o eliminar las auditorías asociadas a dicho plan.</p>

				<center>
					<a data-fancybox="gallery" href="assets/img/Manual/imagen28.PNG">{!! HTML::image('assets/img/Manual/imagen28.PNG',"Imagen no encontrada",['title'=>'Figura 24','width'=>'200', 'height'=>'150']) !!}</a>
					<p style="font-size: 11px;">Figura 24. Información de plan de auditoría.</p>
				</center><br>

			<p>En la Figura 25 se puede observar la ventana principal para la creación de Planes de auditoría. Al seleccionar organización en {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!}, aparecerá la información de evaluación de Riesgos asociados a la organización seleccionada {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!}, además de la información asociada al último Plan de auditoría generado para esta organización {!! HTML::image('assets/img/Manual/3.PNG',"Imagen no encontrada",['title'=>'3','width'=>'20', 'height'=>'20']) !!}.</p>

				<center>
					<a data-fancybox="gallery" href="assets/img/Manual/imagen29.PNG">{!! HTML::image('assets/img/Manual/imagen29.PNG',"Imagen no encontrada",['title'=>'Figura 25','width'=>'200', 'height'=>'150']) !!}</a>
					<p style="font-size: 11px;">Figura 25. Creación de Plan de auditoría.</p>
				</center><br>

			<p>La información que se debe agregar para la creación del plan de auditoría es:</p>

			<ul>
				<li>- Nombre del Plan *</li>
				<li>- Descripción del Plan *</li>
				<li>-	Objetivos</li>
				<li>-	Alcances</li>
				<li>-	Recursos</li>
				<li>-	Auditor responsable</li>
				<li>-	Equipo de auditores</li>
				<li>-	Metodología</li>
				<li>-	Fecha Inicio *</li>
				<li>-	Fecha Término *</li>
				<li>-	Norma(s) Asociada(s)</li>
			</ul>

			<p>Por otra parte, como se observa en la Figura 26, puede ingresar la información de las auditorías asociadas al plan. Haciendo click en Agregar Nueva Auditoría {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!}, puede crear una auditoría ingresando todos los campos descritos en la Figura 26. Por otra parte, en {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!} puede seleccionar auditorías creadas previamente en otros planes, las que podrán ser agregadas al plan actual.</p>

				<center>
					<a data-fancybox="gallery" href="assets/img/Manual/imagen30.PNG">{!! HTML::image('assets/img/Manual/imagen30.PNG',"Imagen no encontrada",['title'=>'Figura 26','width'=>'200', 'height'=>'150']) !!}</a>
					<p style="font-size: 11px;">Figura 26. Creación de auditorías asociadas a Plan.</p>
				</center><br>

			<ol>
				<li><b>Programas de auditor&iacute;a</b><br>
				<p>Esta funcionalidad permite generar programas de auditoría asociados a una auditoría. Para esto, primero debe seleccionar el plan de auditoría y la auditoría donde se desea gestionar los programas. En la Figura 27 se puede observar la ventana principal para la gestión de Programas de Auditoría. En esta sección puede agregar un nuevo programa {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!}, ver y gestionar un programa junto a sus pruebas correspondientes {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!}, o eliminar un programa que no tenga información asociada {!! HTML::image('assets/img/Manual/3.PNG',"Imagen no encontrada",['title'=>'3','width'=>'20', 'height'=>'20']) !!}.</p>

				<center>
					<a data-fancybox="gallery" href="assets/img/Manual/imagen31.PNG">{!! HTML::image('assets/img/Manual/imagen31.PNG',"Imagen no encontrada",['title'=>'Figura 27','width'=>'200', 'height'=>'150']) !!}</a>
					<p style="font-size: 11px;">Figura 27. Ventana principal para gestión de programas de auditoría.</p>
				</center><br>

				<ul>
					<li>- Agregar nuevo programa: En la Figura 28 puede observar el formulario que debe completar ara agregar un nuevo programa de auditoria. Para realizar esto, se puede basar en un programa creado previamente {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!} o generar un nuevo programa desde cero, ingresando los campos del formulario. Además, podrá agregar documentos asociados al programa de auditoría.

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen32.PNG">{!! HTML::image('assets/img/Manual/imagen32.PNG',"Imagen no encontrada",['title'=>'Figura 28','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 28. Creación de Programa de Auditoría.</p>
					</center><br>
					</li>
					<li>- Ver: Como se puede observar en la Figura 29, esta funcionalidad permite ver la información asociada al programa seleccionado, además de la información asociada a las pruebas de auditoría del mismo programa {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!}, y la posibilidad de generar nuevas pruebas de auditoria para el programa {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!}. Por otra parte, también podrá editar el programa de auditoría seleccionado {!! HTML::image('assets/img/Manual/3.PNG',"Imagen no encontrada",['title'=>'3','width'=>'20', 'height'=>'20']) !!}.

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen33.PNG">{!! HTML::image('assets/img/Manual/imagen33.PNG',"Imagen no encontrada",['title'=>'Figura 29','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 29. Gestión de programa de auditoría y sus pruebas.</p>
					</center><br>
					</li>
					<li>- Agregar prueba: Para generar una prueba se debe primero que todo, seleccionar el tipo de prueba, ya sea a nivel de procesos o de entidad. En caso de ser una prueba de entidad, se puede seleccionar de forma optativa perspectiva y controles de negocio. Por el contrario, si la prueba es orientada a procesos, opcionalmente se puede seleccionar proceso, subprocesos y controles de proceso asociados. El fin de esta asociación es la posibilidad de que, al realizar pruebas de auditoría con controles asociados, al ejecutarlas éstas puedan afectar la valoración de los riesgos que están siendo mitigados por dichos controles. Por otra parte, la información que se debe agregar a la prueba es:
						<ul>
							<li>-	Nombre *</li>
							<li>-	Descripción *</li>
							<li>-	Tipo (diseño, efectividad operativa, cumplimiento o sustantiva)</li>
							<li>-	Responsable</li>
							<li>-	Horas-hombre</li>
						</ul>
					</li>
				</ul>
				</li>
				<br>
				<li><b>Ejecución de Auditoría</b>
				<p>A través de esta sección podrán ejecutarse las pruebas de auditoría generadas anteriormente. Para esto, se debe seleccionar Organización, plan de auditoría y auditoría, a través de lo cual aparecerán automáticamente los programas de auditoría generados previamente, como se puede observar en la Figura 30.</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen34.PNG">{!! HTML::image('assets/img/Manual/imagen34.PNG',"Imagen no encontrada",['title'=>'Figura 30','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 30. Ejecución de auditorías.</p>
					</center><br>

				<p>Luego, haciendo click en el botón de Ver pruebas, podrá observar todas las pruebas de auditoría asociadas a cada programa, como se puede observar en la Figura 31.</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen35.PNG">{!! HTML::image('assets/img/Manual/imagen35.PNG',"Imagen no encontrada",['title'=>'Figura 31','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 31. Pruebas de auditorías en sección de ejecución de Auditorías.</p>
					</center><br>

				En estado {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!}, puede cerrar una prueba, para lo cual aparece automáticamente las opciones de “Resultado”, donde debe señalar si la prueba fue efectiva o inefectiva, además de agregar Horas Hombre utilizadas en la prueba. En caso de que la prueba sea inefectiva, podrá gestionar hallazgos para la misma, debiendo primero guardar los resultados de la prueba, al igual que en la evaluación de controles.
				</li>
				<br>
				<li><b>Supervisión de Auditoría</b>
				<p>En esta sección el Auditor Manager podrá agregar notas de revisión a cada una de las pruebas de auditoría. Para esto, deberá seleccionar la organización, plan de auditoría y auditoría, con lo cual se desplegará la información de la prueba y las notas asociadas a éstas.</p>
				<p>Como se puede apreciar en la Figura 32, haciendo click en el botón de Notas, podrá ver información de las notas agregadas (en caso de existir), o podrá agregar nuevas notas (haciendo click en el botón de Agregar nota).</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen36.PNG">{!! HTML::image('assets/img/Manual/imagen36.PNG',"Imagen no encontrada",['title'=>'Figura 32','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 32. Supervisión de auditorías.</p>
					</center><br>

				<p>Para agregar una nota, debe asignar a ésta un nombre y descripción, además de poder cargar documentos opcionalmente. Luego, en esta misma sección podrá revisar las notas agregadas, y también las posibles respuestas por parte del auditor, como se puede apreciar en la Figura 33.</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen37.PNG">{!! HTML::image('assets/img/Manual/imagen37.PNG',"Imagen no encontrada",['title'=>'Figura 33','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 33. Supervisión de prueba de auditoría con nota agregada.</p>
					</center><br>
				</li>

				<li><b>Revisión de Notas</b>
				<p>En esta sección podrá responder las notas generadas en la sección de Supervisión de auditorías, siguiendo el mismo procedimiento descrito en la sección 5.4. Como se puede observar en la Figura 34, se puede observar la vista al expandir las notas agregadas para una prueba, donde se aprecia además la o las respuestas agregadas por el auditor, en donde además se puede eliminar la respuesta señalada, o agregar más respuestas.</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen38.PNG">{!! HTML::image('assets/img/Manual/imagen38.PNG',"Imagen no encontrada",['title'=>'Figura 34','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 34. Respuesta de nota.</p>
					</center><br>
				</li>

				<li><b>Planes de Acción</b>
				<p>Esta función permite la generación de planes de acción asociados a los hallazgos generados en las pruebas de auditoría clasificadas como inefectivas (y en las que se hayan identificado hallazgos). Esta sección posee el mismo formato que las secciones descritas en los puntos anteriores. Como se puede observar en la Figura 35, haciendo click en el botón de Agregar plan de acción, se puede identificar un plan de acción para el hallazgo específico. </p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen39.PNG">{!! HTML::image('assets/img/Manual/imagen39.PNG',"Imagen no encontrada",['title'=>'Figura 35','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 35. Agregar plan de acción para hallazgo de Auditoría.</p>
					</center><br>

				<p>En caso de existir un plan de acción, se mostrará la información de éste sin poder ser modificada (ver Figura 36). Para poder modificar dicho plan de acción se debe ingresar a la sección Mantenedor de Planes de Acción.</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen40.PNG">{!! HTML::image('assets/img/Manual/imagen40.PNG',"Imagen no encontrada",['title'=>'Figura 36','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 36. Plan de acción ya creado para hallazgo de Auditoría.</p>
					</center><br>
				</li>
			</ol>
			</li>
		</ol>
			<br>
			<h4 id="issues"><strong>Mantenedor de Hallazgos</strong>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a id="volvermenu" style="color: #66a2d0; font-size: 10px;" href="#">Volver a Menú</a></h4>
			<hr>

			<p>A través de esta sección se puede ver, crear, modificar o eliminar hallazgos para los distintos tipos posibles. Además, agregando o editando un hallazgo, también es posible definir o editar el plan de acción asociado al mismo.</p>
			<br>
			<h4 id="action_plans"><strong>Mantenedor de Planes de Acción</strong>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a id="volvermenu" style="color: #66a2d0; font-size: 10px;" href="#">Volver a Menú</a></h4>
			<hr>

			<p>El mantenedor de Planes de Acción cumple la misma función que el mantenedor de hallazgos, sin embargo, permite la generación directa de planes de acción para los hallazgos identificados previamente.</p>
			<br>
			<h4 id="reports"><strong>Reportes Básicos</strong>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a id="volvermenu" style="color: #66a2d0; font-size: 10px;" href="#">Volver a Menú</a></h4>
			<hr>

			<p>Los reportes básicos son informes y/o gráficos utilizados para analizar y mostrar información del sistema de forma ordenada. Los reportes que se pueden encontrar dentro de B-GRC son:</p>

			<ol>
				<li><b>Mapa de Riesgos</b>
				<p>Herramienta eficaz e intuitiva que permite el despliegue de los riesgos identificados para las distintas organizaciones dentro de un mapa con distintos colores (según severidad y probabilidad de los riesgos evaluados). En la Figura 37, se puede observar los campos a ingresar para generar el mapa de calor. Como se puede apreciar en esta imagen, se puede generar mapa de calor sólo para los riesgos pertenecientes a la organización, o también para los riesgos de organizaciones dependientes de la seleccionada. Además, se puede generar el mapa sólo para riesgos inherentes (valoración del riesgo a través de encuesta o evaluación manual), o para riesgos inherentes v/s controlados (valoración del riesgo luego de evaluaciones o ejecución de pruebas de auditoría).</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen41.PNG">{!! HTML::image('assets/img/Manual/imagen41.PNG',"Imagen no encontrada",['title'=>'Figura 37','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 37. Opciones de configuración para Mapa de Calor.</p>
					</center><br>

				<p>En las Figuras 38 y 39, se puede apreciar el mapa de calor para riesgos inherentes y para riesgos inherentes v/s controlados respectivamente.</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen42.PNG">{!! HTML::image('assets/img/Manual/imagen42.PNG',"Imagen no encontrada",['title'=>'Figura 38','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 38. Mapa de calor para Riesgos inherentes.</p>
					</center><br>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen43.PNG">{!! HTML::image('assets/img/Manual/imagen43.PNG',"Imagen no encontrada",['title'=>'Figura 39','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 39. Mapa de calor para Riesgos inherentes v/s controlados.</p>
					</center><br>
				</li>

				<li><b>Matriz de Riesgos</b>
				<p>Representa de forma ordenada y calificada cada uno de los elementos asociados a los riesgos identificados previamente, con el fin de realizar una adecuada gestión sobre éstos. Es exportable a Excel (a través del botón Exportar). En la Figura 40 se puede observar una matriz de ejemplo, la cual servirá de ejemplo para todos los reportes del mismo tipo.</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen44.PNG">{!! HTML::image('assets/img/Manual/imagen44.PNG',"Imagen no encontrada",['title'=>'Figura 40','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 40. Matriz de Riesgos.</p>
					</center><br>

				</li>
				
				<li><b>Matriz de Controles</b>
				<p>Representa de forma ordenada toda la información respectiva a los controles generados en el sistema (a través del mantenedor de controles).</p>
				</li>

				<li><b>Hallazgos</b>
				<p>A través de este reporte se puede obtener la información relevante a los hallazgos generados en el sistema, filtrando éstos por tipo de hallazgo y por organización.</p>
				</li>

				<li><b>Planes de auditoría</b>
				<p>Este reporte muestra la información asociada a los planes de auditoría generados en el sistema. Es importante destacar que, a través de este reporte, se puede identificar el tiempo el cumplimiento de los tiempos identificados para los planes de auditoría, a través de las horas planificadas para cada prueba del plan v/s las horas reales de ejecución, como se aprecia en {!! HTML::image('assets/img/Manual/1.PNG',"Imagen no encontrada",['title'=>'1','width'=>'20', 'height'=>'20']) !!} en la Figura 41. Además, haciendo click en las auditorías {!! HTML::image('assets/img/Manual/2.PNG',"Imagen no encontrada",['title'=>'2','width'=>'20', 'height'=>'20']) !!}, puede desplegar la información asociada a éstas.</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen45.PNG">{!! HTML::image('assets/img/Manual/imagen45.PNG',"Imagen no encontrada",['title'=>'Figura 41','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 41. Reporte de planes de auditoría.</p>
					</center><br>
				</li>

				<li><b>Planes de Acción</b>
				<p>Información relevante a los planes de acción generados en el sistema.</p>
				</li>

				<li><b>Reportes de Gráficos</b>
				<p>Se pueden observar gráficos de torta de distintos elementos asociados al sistema. Estos gráficos disponibles son:</p>

					<ol>
						<li>Gráficos de Controles</li>
						<li>Gráficos de Auditorías</li>
						<li>Gráficos de Planes de Acción</li>
					</ol>

				<p>Cada una de estas secciones presenta distintos gráficos asociados principalmente al estado de los elementos (por ejemplo, en controles, se puede observar gráfico de controles ejecutados v/s pendientes y también de controles efectivos v/s inefectivos). Además, como se observa en la Figura 42, haciendo click sobre alguno de los estados, se puede observar la información respectiva de los elementos que está mostrando el gráfico.</p>

					<center>
						<a data-fancybox="gallery" href="assets/img/Manual/imagen46.PNG">{!! HTML::image('assets/img/Manual/imagen46.PNG',"Imagen no encontrada",['title'=>'Figura 42','width'=>'200', 'height'=>'150']) !!}</a>
						<p style="font-size: 11px;">Figura 42. Detalles en reporte de gráfico de controles.</p>
					</center><br>
				</li>
			</ol>
			<br>

			<h4 id="docs"><strong>Gestor de Documentos</strong>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a id="volvermenu" style="color: #66a2d0; font-size: 10px;" href="#">Volver a Menú</a></h4>
			<hr>

			<p>El gestor de documentos permite descargar y/o eliminar todos los documentos que han sido cargados en el sistema. Para poder realizar estas acciones, debe filtrar por el elemento al cual desea ver los documentos asociados. En la Figura 43, se puede observar como ejemplo los documentos asociados a un control de proceso. Para descargar algún documento se debe hacer click en el icono que lo representa. Por el contrario, si se desea eliminar un documento, se debe hacer click en la X roja que se encuentra a un costado del nombre del documento.</p>

				<center>
					<a data-fancybox="gallery" href="assets/img/Manual/imagen47.PNG">{!! HTML::image('assets/img/Manual/imagen47.PNG',"Imagen no encontrada",['title'=>'Figura 43','width'=>'200', 'height'=>'150']) !!}</a>
					<p style="font-size: 11px;">Figura 43. Gestor de documentos.</p>
				</center><br>
		</div>
				<center>
					{!! link_to_route('home', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')

<script>
jQuery(document).ready(function($) {
	$('#masterdatamenu').click(function(){
		$('html,body').animate({
				scrollTop: $("#masterdata").offset().top
			}, 700);
	});

	$('#risksmenu').click(function(){
		$('html,body').animate({
				scrollTop: $("#risks").offset().top
			}, 700);
	});

	$('#strategymenu').click(function(){
		$('html,body').animate({
				scrollTop: $("#strategy").offset().top
			}, 700);
	});

	$('#controlsmenu').click(function(){
		$('html,body').animate({
				scrollTop: $("#controls").offset().top
			}, 700);
	});

	$('#auditsmenu').click(function(){
		$('html,body').animate({
				scrollTop: $("#audits").offset().top
			}, 700);
	});

	$('#issuesmenu').click(function(){
		$('html,body').animate({
				scrollTop: $("#issues").offset().top
			}, 700);
	});

	$('#action_plans_menu').click(function(){
		$('html,body').animate({
				scrollTop: $("#action_plans").offset().top
			}, 700);
	});

	$('#reportsmenu').click(function(){
		$('html,body').animate({
				scrollTop: $("#reports").offset().top
			}, 700);
	});

	$('#docsmenu').click(function(){
		$('html,body').animate({
				scrollTop: $("#docs").offset().top
			}, 700);
	});


	$('.volvermenu').click(function(){
		$('html,body').animate({
				scrollTop: $("#menu").offset().top
			}, 500);
	});

	$('#volver').click(function() {
    	window.history.back();
    });

});
</script>
@stop