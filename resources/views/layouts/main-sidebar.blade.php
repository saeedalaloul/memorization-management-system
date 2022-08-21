<div class="container-fluid">
    <div class="row">
        <!-- Left Sidebar start-->
        <div class="side-menu-fixed">

            @if (auth()->user()->current_role == \App\Models\User::ADMIN_ROLE)
                @include('layouts.main-sidebar.admin-main-sidebar')
            @endif

            @if (auth()->user()->current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                @include('layouts.main-sidebar.exams_supervisor-main-sidebar')
            @endif

            @if (auth()->user()->current_role == \App\Models\User::COURSES_SUPERVISOR_ROLE)
                @include('layouts.main-sidebar.courses-supervisor-main-sidebar')
            @endif

            @if (auth()->user()->current_role == \App\Models\User::ACTIVITIES_SUPERVISOR_ROLE)
                @include('layouts.main-sidebar.activities-supervisor-main-sidebar')
            @endif

            @if (auth()->user()->current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
                @include('layouts.main-sidebar.oversight-supervisor-main-sidebar')
            @endif

            @if (auth()->user()->current_role == \App\Models\User::SUPERVISOR_ROLE)
                @include('layouts.main-sidebar.supervisor-main-sidebar')
            @endif

            @if (auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
                @include('layouts.main-sidebar.teacher-main-sidebar')
            @endif

            @if (auth()->user()->current_role == \App\Models\User::TESTER_ROLE)
                @include('layouts.main-sidebar.tester-main-sidebar')
            @endif

            @if (auth()->user()->current_role == \App\Models\User::OVERSIGHT_MEMBER_ROLE)
                @include('layouts.main-sidebar.oversight-member-main-sidebar')
            @endif

            @if (auth()->user()->current_role == \App\Models\User::ACTIVITY_MEMBER_ROLE)
                @include('layouts.main-sidebar.activity-member-main-sidebar')
            @endif

        </div>
        <!-- Left Sidebar End-->
