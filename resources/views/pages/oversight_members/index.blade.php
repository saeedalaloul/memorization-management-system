@extends('layouts.master')
@section('css')
@section('title')
    أعضاء الرقابة
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    أعضاء الرقابة
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:oversight-members/>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render
    <script>
        window.addEventListener('hideDialog', _ => {
            $('#oversightMemberDeleted').modal('hide');
        });
    </script>
@endsection

