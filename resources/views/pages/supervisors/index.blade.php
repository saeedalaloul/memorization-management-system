@extends('layouts.master')
@section('css')
@section('title')
    إدارة المشرفين
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة المشرفين
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:supervisors/>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_render
    <script>
        window.livewire.on('delete_Supervisor', () => {
            $('#delete_Supervisor').modal('hide');
        });
    </script>
@endsection
