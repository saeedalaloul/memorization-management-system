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
            $('#reset_user_password').modal('show');
        });

        window.addEventListener('hideModal', _ => {
            $('#reset_user_password').modal('hide');
        });
    </script>
@endsection
