@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    متابعة الحفظ والمراجعة
@stop
@endsection
@section('page-header')
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
                    <livewire:students-daily-preservation/>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection
@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
    <script>
        window.livewire.on('showDialogShowDailyPreservation', () => {
            $('#show-daily-preservation').modal('show');
        });

        window.livewire.on('showDialogAddDailyPreservation', () => {
            $('#add-daily-preservation').modal('show');
        });

        window.livewire.on('hideDialogAddDailyPreservation', () => {
            $('#add-daily-preservation').modal('hide');
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
