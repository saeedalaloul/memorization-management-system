@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    إدارة اختبارات التجميعي القرآنية
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة اختبارات التجميعي القرآنية
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:exams-summative/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
    <script>
        window.livewire.on('approval-exam', () => {
            $('#approval-exam').modal('hide');
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
