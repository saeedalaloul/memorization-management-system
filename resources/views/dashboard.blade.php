<!DOCTYPE html>
<html lang="en">
@section('title')
    الرئيسية
@stop
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="HTML5 Template"/>
    <meta name="description" content="Webmin - Bootstrap 4 & Angular 5 Admin Dashboard Template"/>
    <meta name="author" content="potenzaglobalsolutions.com"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600&display=swap" rel="stylesheet">
    @include('layouts.head')
    @livewireStyles
</head>

<body style="font-family: 'Cairo', sans-serif">

<div class="wrapper" style="font-family: 'Cairo', sans-serif">

    <!--=================================
preloader -->

    <div id="pre-loader">
        <img src="{{ URL::asset('assets/images/pre-loader/loader-01.svg',true) }}" alt="">
    </div>

    <!--=================================
preloader -->

@include('layouts.main-header')

@include('layouts.main-sidebar')

<!--=================================
 Main content -->
    <!-- main-content -->
    <div class="content-wrapper">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="mb-0" style="font-family: 'Cairo', sans-serif">مرحبا بك : {{auth()->user()->name}}</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb pt-0 pr-0 float-left float-sm-right">
                        الرئيسية
                    </ol>
                </div>
            </div>
        </div>

    @if (auth()->user()->current_role == \App\Models\User::ADMIN_ROLE
         || auth()->user()->current_role == \App\Models\User::SUPERVISOR_ROLE)
        @include('layouts.statistics.admin-statistics')
    @endif

    @if (auth()->user()->current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE
        || auth()->user()->current_role == \App\Models\User::TESTER_ROLE)
        @include('layouts.statistics.exams-supervisor-statistics')
    @endif

    @if (auth()->user()->current_role == \App\Models\User::ACTIVITIES_SUPERVISOR_ROLE
         || auth()->user()->current_role == \App\Models\User::ACTIVITY_MEMBER_ROLE)
        @include('layouts.statistics.activities-supervisor-statistics')
    @endif

    @if (auth()->user()->current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE
         || auth()->user()->current_role == \App\Models\User::OVERSIGHT_MEMBER_ROLE)
        @include('layouts.statistics.oversight-supervisor-statistics')
    @endif

    @if (auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
        @include('layouts.statistics.teacher-statistics')
    @endif

    <!-- widgets -->
        <div class="row">
            <div class="col-xl-12 mb-30">
                {{--                <livewire:calendar/>--}}
            </div>
        </div>

        <!--=================================
wrapper -->

        <!--=================================
footer -->
        @include('layouts.footer')
    </div><!-- main content wrapper end-->
</div>
<!--=================================
footer -->

@include('layouts.footer-scripts')
@livewireScripts
@stack('scripts')
</body>

</html>
