@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    إدارة أدوار صندوق الشكاوي والإقتراحات
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة أدوار صندوق الشكاوي والإقتراحات
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:complaint-box-roles/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
    <script>
        window.livewire.on('complaintBoxRoleAdded', () => {
            $('#complaintBoxRoleAdded').modal('hide');
        });

        window.livewire.on('complaintBoxRoleEdited', () => {
            $('#complaintBoxRoleEdited').modal('hide');
        });
        window.livewire.on('complaintBoxRoleDeleted', () => {
            $('#complaintBoxRoleDeleted').modal('hide');
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
