@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    اختبارات التجميعي اليوم
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    اختبارات التجميعي اليوم
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:today-exams-summative/>
        </div>
    </div>
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

        window.livewire.on('showModal', () => {
            $('#exam-question-count-select').modal('show');
        });

        window.livewire.on('exam-question-count-select', () => {
            $('#exam-question-count-select').modal('hide');
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
