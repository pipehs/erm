<div class="form-group">
	<label for="organization" class="col-sm-4 control-label">Organización<a href="#" class="popper" data-popbox="pop1">?</a></label>
	<div class="col-sm-4">
		@if (isset($data['organization']))
			{!!Form::text('organization',$data['organization'],['id'=>'organization','class'=>'form-control','required'=>'true'])!!}
		@else
			{!!Form::text('organization',null,['id'=>'organization','class'=>'form-control','required'=>'true'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="version" class="col-sm-4 control-label">Versión del sistema<a href="#" class="popper" data-popbox="pop2">?</a></label>
	<div class="col-sm-4">
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
	<div class="col-sm-3">
		@if (isset($data['system_url']))
			{!!Form::text('system_url',$data['system_url'],['id'=>'system_url','class'=>'form-control','required'=>'true'])!!}
		@else
			{!!Form::text('system_url',null,['id'=>'system_url','class'=>'form-control','required'=>'true'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="alert_ap_message_expired" class="col-sm-4 control-label">Mensaje alerta Plan de acción (expirado)<a href="#" class="popper" data-popbox="pop4">?</a></label>
	<div class="col-sm-4">
		@if (isset($data['alert_ap_message_expired']))
			{!!Form::textarea('alert_ap_message_expired',$data['alert_ap_message_expired'],['id'=>'alert_ap_message_expired','class'=>'form-control','required'=>'true'])!!}
		@else
			{!!Form::textarea('alert_ap_message_expired',null,['id'=>'alert_ap_message_expired','class'=>'form-control','required'=>'true'])!!}
		@endif
	</div>
</div>

<div class="form-group">
	<label for="alert_ap_message_to_expire" class="col-sm-4 control-label">Mensaje alerta Plan de acción (por expirar)<a href="#" class="popper" data-popbox="pop5">?</a></label>
	<div class="col-sm-4">
		@if (isset($data['alert_ap_message_to_expire']))
			{!!Form::textarea('alert_ap_message_to_expire',$data['alert_ap_message_to_expire'],['id'=>'alert_ap_message_to_expire','class'=>'form-control','required'=>'true'])!!}
		@else
			{!!Form::textarea('alert_ap_message_to_expire',null,['id'=>'alert_ap_message_to_expire','class'=>'form-control','required'=>'true'])!!}
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