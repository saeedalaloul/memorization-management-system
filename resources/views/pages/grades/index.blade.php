@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    إدارة المراحل
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة المراحل
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:grades/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
    <script>
        window.livewire.on('gradeAdded', () => {
            $('#gradeAdded').modal('hide');
        });

        window.livewire.on('gradeEdited', () => {
            $('#gradeEdited').modal('hide');
        });
        window.livewire.on('gradeDeleted', () => {
            $('#gradeDeleted').modal('hide');
        });
    </script>

    <script>
        window.addEventListener('alert', event => {
            toastr[event.detail.type](event.detail.message,
                event.detail.title ?? ''), toastr.options = {
                "progressBar": true,
            }
        });
    </script>
@endsection
