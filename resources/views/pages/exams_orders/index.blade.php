@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    طلبات الإختبارات
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    طلبات الإختبارات
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:exams-orders/>
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
@endsection
