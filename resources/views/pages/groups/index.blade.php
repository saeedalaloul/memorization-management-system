@extends('layouts.master')
@section('title')
    إدارة الحلقات
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة الحلقات
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:groups/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render

    <script>
        window.addEventListener('hideDialog', _ => {
            $('#groupDeleted').modal('hide');
            $('#groupPullTeacher').modal('hide');
        });

        window.addEventListener('showDialog', _ => {
            $('#groupMove').modal('show');
        });

        window.addEventListener('showModalPullTeacher', _ => {
            $('#groupPullTeacher').modal('show');
        });

        window.addEventListener('showModalDeleteGroup', _ => {
            $('#groupDeleted').modal('show');
        });
    </script>
@endsection

