@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    إدارة المحفظين
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة المحفظين
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:teachers/>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
    <script>
        window.livewire.on('delete_Teacher', () => {
            $('#delete_Teacher').modal('hide');
        });
    </script>
@endsection
