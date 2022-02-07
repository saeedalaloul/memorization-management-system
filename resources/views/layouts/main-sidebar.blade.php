<div class="container-fluid">
    <div class="row">
        <!-- Left Sidebar start-->
        <div class="side-menu-fixed">

            @if (auth()->user()->current_role == 'أمير المركز')
                @include('layouts.main-sidebar.admin-main-sidebar')
            @endif

            @if (auth()->user()->current_role == 'مشرف الإختبارات')
                @include('layouts.main-sidebar.supervisor-exams-main-sidebar')
            @endif

            @if (auth()->user()->current_role == 'مشرف الدورات')
                @include('layouts.main-sidebar.courses-supervisor-main-sidebar')
            @endif

            @if (auth()->user()->current_role == 'مشرف الأنشطة')
                @include('layouts.main-sidebar.activities-supervisor-main-sidebar')
            @endif

            @if (auth()->user()->current_role == 'مشرف الرقابة')
                @include('layouts.main-sidebar.oversight-supervisor-main-sidebar')
            @endif

            @if (auth()->user()->current_role == 'مشرف')
                @include('layouts.main-sidebar.supervisor-main-sidebar')
            @endif

            @if (auth()->user()->current_role == 'إداري')
                @include('layouts.main-sidebar.lower-supervisor-main-sidebar')
            @endif

            @if (auth()->user()->current_role == 'محفظ')
                @include('layouts.main-sidebar.teacher-main-sidebar')
            @endif

            @if (auth()->user()->current_role == 'مختبر')
                @include('layouts.main-sidebar.tester-main-sidebar')
            @endif

        </div>
        <!-- Left Sidebar End-->
