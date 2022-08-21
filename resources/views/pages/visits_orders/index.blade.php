@extends('layouts.master')
@section('css')
@section('title')
    طلبات زيارات الرقابة
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    طلبات زيارات الرقابة
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:visits-orders/>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render
@endsection
