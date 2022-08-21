@extends('layouts.master')
@section('css')
@section('title')
    إقرار زيارة على الحلقات
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إقرار زيارة على الحلقات
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:select-visit-group/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render
    <script>
        window.addEventListener('hideDialog', _ => {
            $('#select-visit').modal('hide');
        });
    </script>
@endsection

