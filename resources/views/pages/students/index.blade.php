@extends('layouts.master')
@section('css')
    @toastr_css
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
    @toastr_js
    @toastr_render
    <script>
        window.addEventListener('alert', event => {
            toastr[event.detail.type](event.detail.message,
                event.detail.title ?? ''), toastr.options = {
                "progressBar": true,
            }
        });

        window.addEventListener('hideDialog', () => {
            $('#warning_cancel').modal('hide');
            $('#block_cancel').modal('hide');
            $('#add-exam').modal('hide');
            $('#reset-data-daily-preservation').modal('hide');
        });

        window.addEventListener('showDialog', () => {
            $('#add-exam').modal('show');
        });

        window.addEventListener('showDialogDailyPreservation', () => {
            $('#reset-data-daily-preservation').modal('show');
        });
    </script>
@endsection
