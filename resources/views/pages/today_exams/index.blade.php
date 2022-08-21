@extends('layouts.master')
@section('css')
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
    @toastr_render

    <script>
        window.addEventListener('showModal', _ => {
            $('#exam-question-count-select').modal('show');
        });

        window.addEventListener('hideModal', _ => {
            $('#exam-question-count-select').modal('hide');
            $('#approval-exam').modal('hide');
            $('#exam-question-count-select').modal('hide');
        });
    </script>
@endsection
