@extends('layouts.master')
@section('css')
@section('title')
    إدارة مشرفي الحلقات المكفولة
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة مشرفي الحلقات المكفولة
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:sponsorship-supervisors/>
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
