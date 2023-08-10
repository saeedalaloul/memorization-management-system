@extends('layouts.master')
@section('css')
@section('title')
    إدارة أدوار المستخدمين
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة أدوار المستخدمين
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:roles/>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_render

    <script>
        window.addEventListener('showDialogEditPermission', _ => {
            $('#edit-permission').modal('show');
        });

        window.addEventListener('hideModal', _ => {
            $('#edit-permission').modal('hide');
        });
    </script>
@endsection
