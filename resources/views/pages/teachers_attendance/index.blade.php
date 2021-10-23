@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    حضور وغياب المحفظين
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    حضور وغياب المحفظين
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <livewire:teachers-attendance/>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
@endsection
