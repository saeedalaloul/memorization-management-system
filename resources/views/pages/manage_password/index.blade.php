@extends('layouts.master')
@section('css')
@section('title')
    تغيير كلمة المرور
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    تغيير كلمة المرور
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:manage-password/>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_render
@endsection
