@extends('layouts.master')
@section('css')
@section('title')
    زيارات الرقابة اليوم
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    زيارات الرقابة اليوم
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:today-visits/>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render
@endsection
