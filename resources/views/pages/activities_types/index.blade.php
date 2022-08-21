@extends('layouts.master')
@section('title')
    أنواع الأنشطة
@stop
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    أنواع الأنشطة
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:activities-types/>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render

    <script>
        window.addEventListener('hideDialog', _ => {
            $('#activityTypeDeleted').modal('hide');
        });
    </script>
@endsection
