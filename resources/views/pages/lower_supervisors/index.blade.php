@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    إدارة الإداريين
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة الإداريين
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:lower-supervisors/>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
    <script>
        window.livewire.on('delete_LowerSupervisor', () => {
            $('#delete_LowerSupervisor').modal('hide');
        });
    </script>
@endsection
