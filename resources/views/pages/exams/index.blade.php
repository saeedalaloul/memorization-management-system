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
            $('#assign-external-exam-mark').modal('hide');
        });
        window.addEventListener('showModal', _ => {
            $('#assign-external-exam-mark').modal('show');
        });
    </script>
@endsection
