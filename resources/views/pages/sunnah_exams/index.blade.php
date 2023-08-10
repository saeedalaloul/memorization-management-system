@extends('layouts.master')
@section('css')
@section('title')
    إدارة اختبارات السنة
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة اختبارات السنة
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:sunnah-exams/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render

    <script>
        window.addEventListener('hideModal', _ => {
            $('#approval-exam').modal('hide');
            $('#manage-exam').modal('hide');
            $('#manage-external-exam').modal('hide');
        });

        window.addEventListener('showModalManageExam', _ => {
            $('#manage-exam').modal('show');
        });

        window.addEventListener('showModalManageExternalExam', _ => {
            $('#manage-external-exam').modal('show');
        });
    </script>
@endsection
