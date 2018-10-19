<li>
	<a href="documentos" class="{{ activeMenu('documentos') }}">
	@if (Session::get('org') == 'B-GRC Deloitte')
		<i class="fa fa-plus"></i>
	@else
		<i class="fa fa-cloud"></i>
	@endif
		<span class="hidden-xs">Gestor de Documentos</span>
	</a>
</li>