@extends('layouts.master')
@section('css')
@section('title')
    إدارة الاجراءات العقابية
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة الاجراءات العقابية
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:punitive-measures/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render
    <script>
        window.addEventListener('showModal', _ => {
            $('#select-group-custom').modal('show');
        });

        window.addEventListener('showModalDelete', _ => {
            $('#delete-punitive-measure').modal('show');
        });

        window.addEventListener('hideModal', _ => {
            $('#select-group-custom').modal('hide');
            $('#delete-punitive-measure').modal('hide');
        });
    </script>
@endsection
