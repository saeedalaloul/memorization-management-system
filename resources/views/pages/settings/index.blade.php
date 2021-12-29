@extends('layouts.master')
@section('css')
    @toastr_css
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
    @toastr_js
    @toastr_render

    <script>
        window.addEventListener('alert', event => {
            toastr[event.detail.type](event.detail.message,
                event.detail.title ?? ''), toastr.options = {
                "progressBar": true,
            }
        });
    </script>

    <script>
        window.addEventListener('keydown', event => {
            document.getElementById("loading_indicator").innerHTML = "";
        });
    </script>
@endsection
