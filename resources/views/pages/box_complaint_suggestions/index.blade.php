@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    إدارة صندوق الشكاوي والإقتراحات
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    إدارة صندوق الشكاوي والإقتراحات
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <livewire:box-complaint-suggestions/>
    <!-- row closed -->
@endsection

@section('js')
    @livewireScripts
    @toastr_js
    @toastr_render
    <script>
        window.livewire.on('approval-exam', () => {
            $('#approval-exam').modal('hide');
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
    <!-- summernote css/js -->
    <link rel="stylesheet" href="{{asset('assets/js/summernote/summernote-bs4.min.css',true)}}"/>
    <script src="{{asset('assets/js/summernote/summernote-bs4.min.js',true)}}"></script>
    <script type="text/javascript">
        $('#summernote').summernote({
            tabsize: 2,
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
        });
    </script>
@endsection
