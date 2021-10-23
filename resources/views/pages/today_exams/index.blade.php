@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    اختبارات اليوم
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    اختبارات اليوم
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:today-exams/>
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
@endsection
