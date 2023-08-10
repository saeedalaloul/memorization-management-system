@extends('layouts.master')
@section('css')
@section('title')
    تقرير الحفظ والمراجعة قسم (السنة)
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    تقرير الحفظ والمراجعة قسم (السنة)
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <livewire:report-daily-memorization-sunnah/>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_render
@endsection
