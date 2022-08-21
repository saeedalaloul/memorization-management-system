@extends('layouts.master')
@section('css')
@section('title')
    أعضاء الأنشطة
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    أعضاء الأنشطة
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:activity-members/>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render

    <script>
        window.addEventListener('hideDialog', _ => {
            $('#activityMemberDeleted').modal('hide');
        });
    </script>
@endsection

