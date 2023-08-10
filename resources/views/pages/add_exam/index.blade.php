@extends('layouts.master')
@section('css')
@section('title')
    إضافة اختبار
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إضافة اختبار
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:add-exam/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render

    <script>
        window.addEventListener('hideModal', _ => {
            $('#add-exam-modal').modal('hide');
        });
        window.addEventListener('showModal', _ => {
            $('#add-exam-modal').modal('show');
        });
    </script>
@endsection
