@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    طلبات اختبارات التجميعي
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    طلبات اختبارات التجميعي
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:exams-summative-orders/>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
    <script>
        window.livewire.on('refusal-exam', () => {
            $('#refusal-exam').modal('hide');
        });

        window.livewire.on('approval-exam', () => {
            $('#approval-exam').modal('hide');
        });

        window.livewire.on('delete-exam-order', () => {
            $('#delete-exam-order').modal('hide');
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
