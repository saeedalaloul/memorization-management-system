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
        window.livewire.on('add-exam', () => {
            $('#add-exam').modal('hide');
        });

        window.livewire.on('showDialogExamRequest', () => {
            $('#add-exam').modal('show');
        });
    </script>
@endsection
