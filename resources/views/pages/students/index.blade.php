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
    <livewire:manage-student-sunnah/>
    <livewire:move-student/>
    <livewire:submit-order-exam/>
    <livewire:submit-order-exam-sunnah/>
    <livewire:reset-daily-memorization/>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_render
    <script>
        window.addEventListener('hideDialog', () => {
            $('#add-exam').modal('hide');
            $('#warning_cancel').modal('hide');
            $('#block_cancel').modal('hide');
            $('#reset-data-daily-memorization').modal('hide');
            $('#add-student-sunnah').modal('hide');
            $('#move-student').modal('hide');
            $('#submit-order-exam').modal('hide');
            $('#submit-order-exam-sunnah').modal('hide');
        });

        window.addEventListener('showModalSubmitOrderExam', () => {
            $('#submit-order-exam').modal('show');
        });

        window.addEventListener('showModalSubmitOrderExamSunnah', () => {
            $('#submit-order-exam-sunnah').modal('show');
        });

        window.addEventListener('showModalMoveStudent', () => {
            $('#move-student').modal('show');
        });

        window.addEventListener('showModalAddStudentSunnah', () => {
            $('#add-student-sunnah').modal('show');
        });

        window.addEventListener('showDialogDailyMemorization', () => {
            $('#reset-data-daily-memorization').modal('show');
        });
    </script>
@endsection
