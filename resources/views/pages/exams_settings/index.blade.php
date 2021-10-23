@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    اعدادات الإختبارات
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    اعدادات الإختبارات
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <livewire:exams-settings/>
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
    </script>
@endsection
