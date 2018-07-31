<!-- jQuery 3 -->
<script src="{{asset('asset/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{asset('asset/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('asset/bower_components/fastclick/lib/fastclick.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('asset/dist/js/adminlte.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{asset('asset/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')}}"></script>
<!-- jvectormap  -->
<script src="{{asset('asset/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{asset('asset/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
<!-- SlimScroll -->
<script src="{{asset('asset/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset('asset/bower_components/chart.js/Chart.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('asset/dist/js/pages/dashboard2.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('asset/dist/js/demo.js')}}"></script>
<!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <script type="text/javascript">
        $(function () {
            // Navigation active
            $('ul.navbar-nav a[href="{{ "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" }}"]').closest('li').addClass('active');
        });
    </script>

    {!! (isset($footer_script))?$footer_script:'' !!}
    @yield('scripts')