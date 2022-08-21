@extends('layouts.master')
@section('css')
@section('title')
    متابعة الحفظ والمراجعة
@stop
@endsection
@section('page-header')
    @livewireStyles
    <!-- breadcrumb -->
@section('PageTitle')
    متابعة الحفظ والمراجعة
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <livewire:students-daily-memorization/>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_render
    <script>
        window.livewire.on('showDialogShowDailyMemorization', () => {
            $('#show-daily-memorization').modal('show');
        });

        window.livewire.on('showDialogAddDailyMemorization', () => {
            $('#add-daily-memorization').modal('show');
        });

        window.livewire.on('hideDialogAddDailyMemorization', () => {
            $('#add-daily-memorization').modal('hide');
        });
    </script>
@endsection
