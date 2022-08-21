@extends('layouts.master')
@section('css')
@section('title')
    الإعدادات
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    الإعدادات
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:settings/>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_render
@endsection
