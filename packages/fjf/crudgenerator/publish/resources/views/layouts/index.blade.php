<!DOCTYPE html>
<html>
@include('layouts.html')
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		@include('layouts.header')
		@include('layouts.sidebar')
			@yield('content')
				<footer class="main-footer">
				    <div class="pull-right hidden-xs">
				      <b>Version</b> 2.4.0
				    </div>
				    <strong>Copyright &copy; 2014-2016 <a href="https://adminlte.io">Almsaeed Studio</a>.</strong> All rights
				    reserved.
				</footer>
	</div>
	@include('layouts.script')
    	@yield('script')
</body>
</html>