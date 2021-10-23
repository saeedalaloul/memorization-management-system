@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    إدارة الحلقات
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة الحلقات
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:groups/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
    <script>
        window.livewire.on('groupMove', () => {
            $('#groupMove').modal('show');
        });

        window.livewire.on('groupMoveClose', () => {
            $('#groupMove').modal('hide');
        });

        window.livewire.on('groupDeleted', () => {
            $('#groupDeleted').modal('hide');
        });
    </script>
@endsection

