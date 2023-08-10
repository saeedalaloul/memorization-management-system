@extends('layouts.master')
@section('css')
@section('title')
    إدارة الإختبارات القرآنية
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة الإختبارات القرآنية
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:exams/>
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
