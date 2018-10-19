<li>
	<a href="hallazgos" class="{{ activeMenu('hallazgos') }}">
	@if (Session::get('org') == 'B-GRC Deloitte')
		<i class="fa fa-plus"></i>
	@else
		<i class="fa fa-search-minus"></i>
	@endif
		<span class="hidden-xs">Hallazgos</span>
	</a>
</li>
