@extends('layouts.master')
@section('css')
@section('title')
    زيارات الرقابة
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    زيارات الرقابة
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:visits/>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_render
    <script>
        window.addEventListener('hideDialog', _ => {
            $('#visit-processing').modal('hide');
        });
    </script>
@endsection
