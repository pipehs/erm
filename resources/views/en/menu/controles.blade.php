<li class="dropdown">
	<a href="#" class="dropdown-toggle">
		<i class="fa fa-wrench"></i>
		<span class="hidden-xs">Control Management</span>
	</a>
	<ul class="dropdown-menu" style="{{ dropDown5() }}">
		<li><a href="controles" class="{{ activeMenu('controles') }}">Controls Mantainer</a></li>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<li><a href="evaluar_controles" class="{{ activeMenu('evaluar_controles') }}">Controls Assessment</a></li>
		<?php break; ?>
		@endif
	@endforeach
	</ul>
</li>