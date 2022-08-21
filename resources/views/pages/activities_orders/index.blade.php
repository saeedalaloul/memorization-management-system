@extends('layouts.master')
@section('css')
@section('title')
    طلبات الأنشطة
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    طلبات الأنشطة
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:activities-orders/>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render

    <script>
        window.addEventListener('showDialog', _ => {
            $('#activity-request').modal('show');
        });

        window.addEventListener('hideDialog', _ => {
            $('#activity-request').modal('hide');
            $('#approval-activity').modal('hide');
            $('#refusal-activity').modal('hide');
            $('#failed-activity').modal('hide');
            $('#delete-activity-order').modal('hide');
        });
    </script>
@endsection

