@extends('layouts.master')
@section('title')
    إدارة المراحل
@stop
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة المراحل
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:grades/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render

    <script>
        window.addEventListener('hideDialog', _ => {
            $('#gradeAdded').modal('hide');
            $('#gradeEdited').modal('hide');
            $('#gradeDeleted').modal('hide');
        });
    </script>
@endsection
