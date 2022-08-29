<!--=================================
 header start-->

<nav class="admin-header navbar navbar-default col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <!-- logo -->
    <div class="text-left navbar-brand-wrapper">
        <a class="navbar-brand brand-logo" href="#"><img src="{{asset('assets/images/logo-dark.png',true)}}" alt=""></a>
        <a class="navbar-brand brand-logo-mini" href="#"><img src="{{asset('assets/images/logo-icon-dark.png',true)}}"
                                                              alt=""></a>
    </div>
    <!-- Top bar left -->
    <ul class="nav navbar-nav mr-auto">
        <li class="nav-item">
            <a id="button-toggle" class="button-toggle-nav inline-block ml-20 pull-left" href="javascript:void(0);"><i
                    class="zmdi zmdi-menu ti-align-right"></i></a>
        </li>
        <li class="nav-item">
            <div class="search">
                <a class="search-btn not_click" href="javascript:void(0);"></a>
                <div class="search-box not-click">
                    <input type="text" class="not-click form-control" placeholder="Search" value="" name="search">
                    <button class="search-button" type="submit"><i class="fa fa-search not-click"></i></button>
                </div>
            </div>
        </li>
    </ul>
    <!-- top bar right -->
    <ul class="nav navbar-nav ml-auto">
        @if (count(auth()->user()->roles) > 1)
            <div class="btn-group mb-1">
                <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    @if (auth()->user()->current_role != null)
                        @if (auth()->user()->current_role == \App\Models\User::ADMIN_ROLE)
                            {{\App\Models\User::ADMIN_ROLE}}
                            <img style="width: 23px; height: 17px;" src="{{ URL::asset('assets/images/admin.png') }}"
                                 alt="">
                        @elseif(auth()->user()->current_role == \App\Models\User::SUPERVISOR_ROLE)
                            {{\App\Models\User::SUPERVISOR_ROLE }}
                            <img style="width: 23px; height: 17px;" src="{{ URL::asset('assets/images/teacher.png') }}"
                                 alt="">
                        @elseif(auth()->user()->current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                            {{\App\Models\User::EXAMS_SUPERVISOR_ROLE }}
                            <img style="width: 23px; height: 17px;" src="{{ URL::asset('assets/images/admin.png') }}"
                                 alt="">
                        @elseif(auth()->user()->current_role == \App\Models\User::COURSES_SUPERVISOR_ROLE)
                            {{\App\Models\User::COURSES_SUPERVISOR_ROLE }}
                            <img style="width: 23px; height: 17px;" src="{{ URL::asset('assets/images/admin.png') }}"
                                 alt="">
                        @elseif(auth()->user()->current_role == \App\Models\User::ACTIVITIES_SUPERVISOR_ROLE)
                            {{\App\Models\User::ACTIVITIES_SUPERVISOR_ROLE }}
                            <img style="width: 23px; height: 17px;" src="{{ URL::asset('assets/images/admin.png') }}"
                                 alt="">
                        @elseif(auth()->user()->current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
                            {{\App\Models\User::OVERSIGHT_SUPERVISOR_ROLE }}
                            <img style="width: 23px; height: 17px;" src="{{ URL::asset('assets/images/admin.png') }}"
                                 alt="">
                        @elseif(auth()->user()->current_role == \App\Models\User::OVERSIGHT_MEMBER_ROLE)
                            {{\App\Models\User::OVERSIGHT_MEMBER_ROLE }}
                            <img style="width: 23px; height: 17px;" src="{{ URL::asset('assets/images/teacher.png') }}"
                                 alt="">
                        @elseif(auth()->user()->current_role == \App\Models\User::ACTIVITY_MEMBER_ROLE)
                            {{\App\Models\User::ACTIVITY_MEMBER_ROLE }}
                            <img style="width: 23px; height: 17px;" src="{{ URL::asset('assets/images/teacher.png') }}"
                                 alt="">
                        @elseif(auth()->user()->current_role == \App\Models\User::TESTER_ROLE)
                            {{\App\Models\User::TESTER_ROLE }}
                            <img style="width: 23px; height: 17px;" src="{{ URL::asset('assets/images/teacher.png') }}"
                                 alt="">
                        @elseif(auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
                            {{\App\Models\User::TEACHER_ROLE }}
                            <img style="width: 23px; height: 17px;" src="{{ URL::asset('assets/images/teacher.png') }}"
                                 alt="">
                        @endif
                    @endif
                </button>
                <div class="dropdown-menu">
                    @for ($i = 0; $i < count(auth()->user()->roles); $i++)
                        <form method="post" action="{{route('switch_account')}}">
                            @csrf
                            <input value="{{auth()->user()->roles[$i]->name}}" name="current_role" hidden>
                            <button type="submit" class="dropdown-item" rel="alternate"
                                    href="#">{{auth()->user()->roles[$i]->name}}</button>
                        </form>
                    @endfor
                </div>
            </div>
        @endif

        <li class="nav-item fullscreen">
            <a id="btnFullscreen" href="#" class="nav-link"><i class="ti-fullscreen"></i></a>
        </li>
        @php
            $count = auth()->user()->unreadNotifications->count();
            $unreadNotifications = auth()->user()->unreadNotifications()->take(10)->get();
        @endphp
        <li class="nav-item dropdown">
            <a class="nav-link top-nav" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
               aria-expanded="false">
                <i class="ti-bell"></i>
                <span class="badge badge-danger notification-status">{{$count == 0? '':' '}}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-big dropdown-notifications">
                <div class="dropdown-header notifications">
                    <strong>الإشعارات</strong>
                    <span class="badge badge-pill badge-warning">{{$count}}</span>
                </div>
                <div class="dropdown-divider"></div>
                @if (count($unreadNotifications) > 0)
                    @foreach($unreadNotifications as $key => $value)
                        @if($value->type == 'App\Notifications\AcceptExamOrderForTeacherNotify'
                             || $value->type == 'App\Notifications\AcceptExamOrderForTesterNotify'
                             || $value->type == 'App\Notifications\FailureExamOrderForTeacherNotify'
                             || $value->type == 'App\Notifications\ImproveExamOrderForExamsSupervisorNotify'
                             || $value->type == 'App\Notifications\NewExamOrderForExamsSupervisorNotify'
                             || $value->type == 'App\Notifications\RejectionExamOrderForTeacherNotify')
                            @include('layouts.notifications.exam-orders-notifications')
                        @elseif($value->type == 'App\Notifications\NewExamForTeacherNotify' ||
                                 $value->type == 'App\Notifications\ImproveExamForTeacherNotify')
                            @include('layouts.notifications.exams-notifications')
                        @elseif($value->type == 'App\Notifications\NewBoxComplaintSuggestionNotify' ||
                                 $value->type == 'App\Notifications\ReplayBoxComplaintSuggestionNotify')
                            @include('layouts.notifications.box-complaint-suggestions-notifications')
                        @elseif($value->type == 'App\Notifications\NewActivityOrderForActivitiesSupervisorNotify'
                                || $value->type == 'App\Notifications\AcceptActivityOrderForTeacherNotify'
                                || $value->type == 'App\Notifications\AcceptActivityOrderForActivityMemberNotify'
                                || $value->type == 'App\Notifications\RejectionActivityOrderForTeacherNotify'
                                || $value->type == 'App\Notifications\FailureActivityOrderForTeacherNotify')
                            @include('layouts.notifications.activity-orders-notifications')
                        @elseif($value->type == 'App\Notifications\NewStudentWarningForTeacherNotify'
                                 || $value->type == 'App\Notifications\ExpiredStudentWarningForTeacherNotify')
                            @include('layouts.notifications.student-warnings-notifications')
                        @elseif($value->type == 'App\Notifications\NewStudentBlockForTeacherNotify'
                               || $value->type == 'App\Notifications\ExpiredStudentBlockForTeacherNotify')
                            @include('layouts.notifications.student-blocks-notifications')
                        @elseif($value->type == 'App\Notifications\NewVisitOrderForOversightMemberNotify'
                               || $value->type == 'App\Notifications\UpdateVisitOrderForOversightMemberNotify'
                               || $value->type == 'App\Notifications\SendVisitOrderForOversightSupervisorNotify')
                            @include('layouts.notifications.visits-orders-notifications')
                        @elseif($value->type == 'App\Notifications\NewVisitForAdminNotify'
                               || $value->type == 'App\Notifications\ReplyToVisitForOversightSupervisorNotify'
                               || $value->type == 'App\Notifications\SolvedVisitForAdminNotify'
                               || $value->type == 'App\Notifications\FailureProcessingOfVisitForAdminNotify'
                               || $value->type == 'App\Notifications\ReminderOfVisitForAdminNotify'
                               || $value->type == 'App\Notifications\ReminderOfVisitForOversightSupervisorNotify')
                            @include('layouts.notifications.visits-notifications')
                        @endif
                    @endforeach
                @else
                    <p class="text-dark text-center">لا توجد إشعارات</p>
                @endif
            </div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link top-nav" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
               aria-expanded="true"> <i class=" ti-view-grid"></i> </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-big">
                <div class="dropdown-header">
                    <strong>Quick Links</strong>
                </div>
                <div class="dropdown-divider"></div>
                <div class="nav-grid">
                    <a href="#" class="nav-grid-item"><i class="ti-files text-primary"></i><h5>New Task</h5></a>
                    <a href="#" class="nav-grid-item"><i class="ti-check-box text-success"></i><h5>Assign Task</h5>
                    </a>
                </div>
                <div class="nav-grid">
                    <a href="#" class="nav-grid-item"><i class="ti-pencil-alt text-warning"></i><h5>Add Orders</h5>
                    </a>
                    <a href="#" class="nav-grid-item"><i class="ti-truck text-danger "></i><h5>New Orders</h5></a>
                </div>
            </div>
        </li>
        <li class="nav-item dropdown mr-30">
            <a class="nav-link nav-pill user-avatar" data-toggle="dropdown" href="#" role="button"
               aria-haspopup="true"
               aria-expanded="false">
                <img
                    src="{{auth()->user()->profile_photo_url}}"
                    alt="avatar">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header">
                    <div class="media">
                        <div class="media-body">
                            <h5 class="mt-0 mb-0">{{auth()->user()->name}}</h5>
                            <span>{{auth()->user()->phone}}</span>
                            {{--                            <span>{{auth()->user()->current_role}}</span>--}}
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{route('manage_password')}}"><i class="text-info ti-key"></i>تغيير كلمة
                    المرور</a>
                <form action="{{route('logout')}}" method="POST">
                    @csrf
                    <button class="dropdown-item"><i class="text-danger ti-unlock"></i>تسجيل الخروج</button>
                </form>
            </div>
        </li>
    </ul>
</nav>

<!--=================================
 header End-->
