@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    إدارة المختبرين
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة المختبرين
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
        window.livewire.on('delete_tester', () => {
            $('#testerDeleted').modal('hide');
        });
    </script>
@endsection

