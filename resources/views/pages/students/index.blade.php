@extends('layouts.master')
@section('css')
@section('title')
    إدارة الطلاب
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة الطلاب
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:students/>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_render
    <script>
        window.addEventListener('hideDialog', () => {
            $('#warning_cancel').modal('hide');
            $('#block_cancel').modal('hide');
            $('#add-exam').modal('hide');
            $('#reset-data-daily-memorization').modal('hide');
        });

        window.addEventListener('showDialog', () => {
            $('#add-exam').modal('show');
        });

        window.addEventListener('showDialogDailyMemorization', () => {
            $('#reset-data-daily-memorization').modal('show');
        });
    </script>
@endsection
