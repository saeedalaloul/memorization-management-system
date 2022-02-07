@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    إدارة تصنيفات صندوق الشكاوي والإقتراحات
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة تصنيفات صندوق الشكاوي والإقتراحات
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:complaint-box-categories/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
    <script>
        window.livewire.on('complaintBoxCategoryAdded', () => {
            $('#complaintBoxCategoryAdded').modal('hide');
        });

        window.livewire.on('complaintBoxCategoryEdited', () => {
            $('#complaintBoxCategoryEdited').modal('hide');
        });
        window.livewire.on('complaintBoxCategoryDeleted', () => {
            $('#complaintBoxCategoryDeleted').modal('hide');
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
