@extends('layouts.master')
@section('css')
@section('title')
    الأنشطة
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    الأنشطة
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:activities/>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render
@endsection

