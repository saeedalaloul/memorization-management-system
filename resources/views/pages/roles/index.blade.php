@extends('layouts.master')
@section('css')
@section('title')
    إدارة أدوار المستخدمين
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة أدوار المستخدمين
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:roles/>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_render
@endsection
