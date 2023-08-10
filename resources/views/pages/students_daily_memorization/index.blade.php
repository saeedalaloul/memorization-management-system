@extends('layouts.master')
@section('css')
    @section('title')
        متابعة الحفظ والمراجعة
    @stop
    <style>
        #main-selected-suras {
            scroll-behavior: smooth;
        }
    </style>
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
                    @if (auth()->user()->current_role === \App\Models\User::TEACHER_ROLE)
                        @php
                            $group = \App\Models\Group::where('teacher_id',auth()->id())->first();
                        @endphp
                        @if ($group !== null && $group->type === \App\Models\Group::SUNNAH_TYPE)
                            <livewire:students-daily-memorization-sunnah/>
                        @else
                            <livewire:students-daily-memorization/>
                        @endif
                    @else
                        <livewire:students-daily-memorization/>
                    @endif
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

        window.livewire.on('showDialogAddPreviousDailyMemorization', () => {
            $('#add-previous-daily-memorization').modal('show');
        });

        window.livewire.on('hideDialogAddDailyMemorization', () => {
            $('#add-daily-memorization').modal('hide');
            $('#add-previous-daily-memorization').modal('hide');
        });
    </script>
@endsection
