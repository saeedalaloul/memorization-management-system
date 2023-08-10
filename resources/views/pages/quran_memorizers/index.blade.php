@extends('layouts.master')
@section('css')
@section('title')
    إدارة الحفظة
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة الحفظة
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:quran-memorizers/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render
@endsection
