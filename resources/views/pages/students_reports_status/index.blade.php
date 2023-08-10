@extends('layouts.master')
@section('css')
@section('title')
    حالة تقارير الطلاب
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    حالة تقارير الطلاب
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:students-reports-status/>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        window.addEventListener('hideModal', _ => {
            $('#student-whatsapp-edit').modal('hide');
        });

        window.addEventListener('showModalEdit', _ => {
            $('#student-whatsapp-edit').modal('show');
        });
    </script>
@endsection
