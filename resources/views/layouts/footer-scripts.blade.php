<!-- jquery -->
<script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js',true) }}"></script>
<!-- plugins-jquery -->
<script src="{{ URL::asset('assets/js/plugins-jquery.js',true) }}"></script>
<!-- plugin_path -->
<!-- plugin_path -->
<script>var plugin_path = '{{ asset('assets/js',true) }}/';</script>

<!-- chart -->
<script src="{{ URL::asset('assets/js/chart-init.js',true) }}"></script>
<!-- calendar -->
<script src="{{ URL::asset('assets/js/calendar.init.js',true) }}"></script>
<!-- charts sparkline -->
<script src="{{ URL::asset('assets/js/sparkline.init.js',true) }}"></script>
<!-- charts morris -->
<script src="{{ URL::asset('assets/js/morris.init.js',true) }}"></script>
<!-- datepicker -->
<script src="{{ URL::asset('assets/js/datepicker.js',true) }}"></script>
<!-- sweetalert2 -->
<script src="{{ URL::asset('assets/js/sweetalert2.js',true) }}"></script>
<!-- toastr -->
@yield('js')
<script src="{{ URL::asset('assets/js/toastr.js',true) }}"></script>
<!-- validation -->
<script src="{{ URL::asset('assets/js/validation.js',true) }}"></script>
<!-- lobilist -->
<script src="{{ URL::asset('assets/js/lobilist.js',true) }}"></script>
<!-- custom -->
<script src="{{ URL::asset('assets/js/custom.js',true) }}"></script>

<script>
    $(document).ready(function () {
        $('#datatable').DataTable();
    });
</script>


<script src="{{ URL::asset('assets/js/bootstrap-datatables/jquery.dataTables.min.js',true) }}"></script>
<script src="{{ URL::asset('assets/js/bootstrap-datatables/dataTables.bootstrap4.min.js',true) }}"></script>
