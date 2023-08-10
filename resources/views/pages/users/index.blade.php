@extends('layouts.master')
@section('css')
@section('title')
    إدارة المستخدمين
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة المستخدمين
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:users/>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_render

    <script>
        window.addEventListener('showDialogResetPassword', _ => {
            $('#reset-user-password').modal('show');
        });

        window.addEventListener('showDialogEditPermission', _ => {
            $('#edit-permission').modal('show');
        });

        window.addEventListener('hideModal', _ => {
            $('#reset-user-password').modal('hide');
            $('#edit-permission').modal('hide');
        });
    </script>
@endsection
