@extends('layouts.master')
@section('css')
@section('title')
    طلبات الإختبارات
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    طلبات الإختبارات
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:exams-orders/>
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
        window.addEventListener('hideDialog', _ => {
            $('#approval-exam').modal('hide');
            $('#refusal-exam').modal('hide');
            $('#delete-exam-order').modal('hide');
        });

        window.addEventListener('showModalDelete', _ => {
            $('#delete-exam-order').modal('show');
        });
    </script>
@endsection
