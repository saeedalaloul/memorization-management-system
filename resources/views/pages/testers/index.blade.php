@extends('layouts.master')
@section('css')
@section('title')
    المختبرين
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    المختبرين
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
    @toastr_render
    <script>
        window.addEventListener('hideDialog', _ => {
            $('#testerDeleted').modal('hide');
        });
    </script>
@endsection

