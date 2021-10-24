@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    إضافة مختبر
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إضافة مختبر
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:testers/>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
    <script>
        window.livewire.on('add_tester', () => {
            $('#add_tester').modal('hide');
        });
    </script>
@endsection

