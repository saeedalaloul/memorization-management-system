@extends('layouts.master')
@section('css')

@section('title')
    إدارة تتبع تنقلات الطلاب
@stop
@endsection
@section('page-header')
@section('PageTitle')
    إدارة تتبع تنقلات الطلاب
@stop
<!-- breadcrumb -->
@endsection
@section('content')
<!-- row -->
<livewire:track-student-transfers/>
<!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_render
@endsection
