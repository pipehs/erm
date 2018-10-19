<div class="form-group">
	<label for="organization" class="col-sm-4 control-label">Organización<a href="#" class="popper" data-popbox="pop1">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['organization']))
			{!!Form::text('organization',$data['organization'],['id'=>'organization','class'=>'form-control','required'=>'true'])!!}
		@else
			{!!Form::text('organization',null,['id'=>'organization','class'=>'form-control','required'=>'true'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="version" class="col-sm-4 control-label">Versión del sistema<a href="#" class="popper" data-popbox="pop2">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['version']))
			{!!Form::text('version',$data['version'],['id'=>'version','class'=>'form-control','required'=>'true'])!!}
		@else
			{!!Form::text('version',null,['id'=>'version','class'=>'form-control','required'=>'true'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="system_url" class="col-sm-4 control-label">URL del sistema<a href="#" class="popper" data-popbox="pop3">?</a></label>
	<div class="col-sm-1">
		http://
	</div>
	<div class="col-sm-5">
		@if (isset($data['system_url']))
			{!!Form::text('system_url',$data['system_url'],['id'=>'system_url','class'=>'form-control','required'=>'true'])!!}
		@else
			{!!Form::text('system_url',null,['id'=>'system_url','class'=>'form-control','required'=>'true'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="logo" class="col-sm-4 control-label">URL del logo<a href="#" class="popper" data-popbox="pop6">?</a></label>
	<div class="col-sm-3">
		http://"url_sistema"/public/
	</div>
	<div class="col-sm-3">
		@if (isset($data['logo']))
			{!!Form::text('logo',$data['logo'],['id'=>'logoimage','class'=>'form-control'])!!}
		@else
			{!!Form::text('logo',null,['id'=>'logoimage','class'=>'form-control'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="logo_width" class="col-sm-4 control-label">Ancho del logo (en px)<a href="#" class="popper" data-popbox="pop7">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['logo_width']))
			{!!Form::text('logo_width',$data['logo_width'],['id'=>'logo_width','class'=>'form-control'])!!}
		@else
			{!!Form::text('logo_width',null,['id'=>'logo_width','class'=>'form-control'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="logo_height" class="col-sm-4 control-label">Alto del logo (en px)<a href="#" class="popper" data-popbox="pop8">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['logo_height']))
			{!!Form::text('logo_height',$data['logo_height'],['id'=>'logo_height','class'=>'form-control'])!!}
		@else
			{!!Form::text('logo_height',null,['id'=>'logo_height','class'=>'form-control'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="logo_small" class="col-sm-4 control-label">URL logo pequeño (sidebar)<a href="#" class="popper" data-popbox="pop12">?</a></label>
	<div class="col-sm-3">
		http://"url_sistema"/public/
	</div>
	<div class="col-sm-3">
		@if (isset($data['logo_small']))
			{!!Form::text('logo_small',$data['logo_small'],['id'=>'logo_small','class'=>'form-control'])!!}
		@else
			{!!Form::text('logo_small',null,['id'=>'logo_small','class'=>'form-control'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="logo_width_small" class="col-sm-4 control-label">Ancho logo pequeño<a href="#" class="popper" data-popbox="pop7">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['logo_width_small']))
			{!!Form::text('logo_width_small',$data['logo_width_small'],['id'=>'logo_width_smallo_width','class'=>'form-control'])!!}
		@else
			{!!Form::text('logo_width_small',null,['id'=>'logo_width_small','class'=>'form-control'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="logo_height_small" class="col-sm-4 control-label">Alto logo pequeño<a href="#" class="popper" data-popbox="pop8">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['logo_height_small']))
			{!!Form::text('logo_height_small',$data['logo_height_small'],['id'=>'logo_height_small','class'=>'form-control'])!!}
		@else
			{!!Form::text('logo_height_small',null,['id'=>'logo_height_small','class'=>'form-control'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="short_name" class="col-sm-4 control-label">Nombre corto<a href="#" class="popper" data-popbox="pop12">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['short_name']))
			{!!Form::text('short_name',$data['short_name'],['id'=>'short_name','class'=>'form-control'])!!}
		@else
			{!!Form::text('short_name',null,['id'=>'short_name','class'=>'form-control'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="welcome_message" class="col-sm-4 control-label">Mensaje de bienvenida<a href="#" class="popper" data-popbox="pop12">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['welcome_message']))
			{!!Form::textarea('welcome_message',$data['welcome_message'],['class'=>'form-control','id' => 'wysiwig_simple','rows'=>2])!!}
		@else
			{!!Form::textarea('welcome_message',null,['id'=>'wysiwig_simple','class'=>'form-control'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="welcome_description" class="col-sm-4 control-label">Descripción de bienvenida<a href="#" class="popper" data-popbox="pop12">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['welcome_description']))
			{!!Form::textarea('welcome_description',$data['welcome_description'],['class'=>'form-control','id' => 'wysiwig_simple2'])!!}
		@else
			{!!Form::textarea('welcome_description',null,['id'=>'wysiwig_simple2','class'=>'form-control'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="alert_ap_message_expired" class="col-sm-4 control-label">Mensaje alerta Plan de acción (expirado)<a href="#" class="popper" data-popbox="pop4">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['alert_ap_message_expired']))
			{!!Form::textarea('alert_ap_message_expired',$data['alert_ap_message_expired'],['id'=>'alert_ap_message_expired','class'=>'form-control','required'=>'true'])!!}
		@else
			{!!Form::textarea('alert_ap_message_expired',null,['id'=>'alert_ap_message_expired','class'=>'form-control','required'=>'true'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="alert_ap_message_to_expire" class="col-sm-4 control-label">Mensaje alerta Plan de acción (por expirar)<a href="#" class="popper" data-popbox="pop5">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['alert_ap_message_to_expire']))
			{!!Form::textarea('alert_ap_message_to_expire',$data['alert_ap_message_to_expire'],['id'=>'alert_ap_message_to_expire','class'=>'form-control','required'=>'true'])!!}
		@else
			{!!Form::textarea('alert_ap_message_to_expire',null,['id'=>'alert_ap_message_to_expire','class'=>'form-control','required'=>'true'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="alert_ap" class="col-sm-4 control-label">Alertas planes de acción<a href="#" class="popper" data-popbox="pop9">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['alert_ap']) && $data['alert_ap'] == '1')
			<input type="checkbox" value="1" name="alert_ap" id="alert_ap" data-toggle="toggle" data-on="Si" data-off="No" data-width="100" data-offstyle="danger" data-onstyle="success" checked>
		@else
			<input type="checkbox" value="1" name="alert_ap" id="alert_ap" data-toggle="toggle" data-on="Si" data-off="No" data-width="100" data-offstyle="danger" data-onstyle="success">
		@endif
	</div>
</div>

<div class="form-group">
	<label for="alert_ap" class="col-sm-4 control-label">Alertas notas auditoría<a href="#" class="popper" data-popbox="pop10">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['alert_audit_notes']) && $data['alert_audit_notes'] == '1')
			<input type="checkbox" value="1" name="alert_audit_notes" id="alert_audit_notes" data-toggle="toggle" data-on="Si" data-off="No" data-width="100" data-offstyle="danger" data-onstyle="success" checked>
		@else
			<input type="checkbox" value="1" name="alert_audit_notes" id="alert_audit_notes" data-toggle="toggle" data-on="Si" data-off="No" data-width="100" data-offstyle="danger" data-onstyle="success">
		@endif
	</div>
</div>


<h4 style="text-align: center; font-weight: bold;">Canal de denuncias</h4>

<div class="form-group">
	<label for="cc_intro_message" class="col-sm-4 control-label">Mensaje introductorio<a href="#" class="popper" data-popbox="pop11">?</a></label>
	<div class="col-sm-6">
		@if (isset($data['cc_intro_message']))
			{!!Form::textarea('cc_intro_message',$data['cc_intro_message'],['class'=>'form-control','id' => 'wysiwig_simple3'])!!}
		@else
			{!!Form::textarea('cc_intro_message',null,['id'=>'wysiwig_simple3','class'=>'form-control'])!!}
		@endif
	</div>
</div>

<div id="pop1" class="popbox">
	<p>Nombre de la organización que se está habilitando</p>
</div>

<div id="pop2" class="popbox">
	<p>Versión del sistema que se encuentra instalada</p>
</div>

<div id="pop3" class="popbox">
	<p>Esta es la URL que identifica la base del sistema, y el que será tomado para el envío de las encuestas. Por ejemplo: www.b-grc.com</p>
</div>

<div id="pop4" class="popbox">
	<p>Mensaje de alerta que se enviará a través de la sección de alertas de planes de acción vencidos (copie y pegue desde otro ambiente).</p>
</div>

<div id="pop5" class="popbox">
	<p>Mensaje de alerta que se enviará a través de la sección de alertas de planes de acción por vencer (copie y pegue desde otro ambiente).</p>
</div>

<div id="pop6" class="popbox">
	<p>URL del logo de inicio que aparece en el sistema. (ingresar link local. por ej: assets/img/logo.png)</p>
</div>

<div id="pop7" class="popbox">
	<p>Ancho del logo (especificar en px, por ej: 150px).</p>
</div>

<div id="pop8" class="popbox">
	<p>Alto del logo (especificar en px, por ej: 150px).</p>
</div>

<div id="pop9" class="popbox">
	<p>Se enviarán alertas al responsable de un plan de acción al crear un plan de acción (siempre que se especifique el responsable del plan)</p>
</div>

<div id="pop10" class="popbox">
	<p>Se enviarán alertas a quien vaya dirigida una nota de auditoría al crear al crear la misma</p>
</div>

<div id="pop11" class="popbox">
	<p>Mensaje que se mostrará al usuario (denunciante) cuando ingrese a la ventana principal del canal de denuncias.</p>
</div>

<div id="pop12" class="popbox">
	<p>Nombre utilizado para los atributos de estilo (style_"nombre_corto".css).</p>
</div>

<div id="pop12" class="popbox">
	<p>Imagen de logo que aparecerá en la cabecera de sidebar (se utiliza uno distinto al logo principal ya que algunos clientes no han cambiado el fondo)</p>
</div>