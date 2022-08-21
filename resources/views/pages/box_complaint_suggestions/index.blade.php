@extends('layouts.master')
@section('css')
@section('title')
    إدارة صندوق الشكاوي والإقتراحات
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة صندوق الشكاوي والإقتراحات
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:box-complaint-suggestions/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render
@endsection
