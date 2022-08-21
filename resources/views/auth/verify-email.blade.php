@extends('layouts.master')
@section('css')
@section('title')
    التحقق من البريد الإلكتروني
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    التحقق من البريد الإلكتروني
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="mb-4 text-sm text-gray-600">
                        شكرا لتسجيلك! قبل البدء ، هل يمكنك التحقق من عنوان بريدك الإلكتروني من خلال النقر على الرابط الذي أرسلناه
                        إليك عبر البريد الإلكتروني للتو؟ إذا لم تتلق البريد الإلكتروني ، فسنرسل لك رسالة أخرى بكل سرور.
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-4 font-medium text-sm text-green-600">
                            تم إرسال رابط تحقق جديد إلى عنوان البريد الإلكتروني الذي قدمته أثناء التسجيل.
                        </div>
                    @endif

                    <div class="mt-4 flex items-center justify-between">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <div>
                                <button class="btn btn-outline-success" type="submit">
                                    إعادة ارسال بريد التحقق
                                </button>
                            </div>
                        </form>
                        <br>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" class="btn btn-outline-danger">
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection

