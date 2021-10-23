<div class="container-fluid">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="side-menu-fixed">
            <div class="scrollbar side-menu-bg" style="overflow: scroll">
                <ul class="nav navbar-nav side-menu" id="sidebarnav">
                    <!-- menu item Dashboard-->
                    <li>
                        <a href="{{ url('/dashboard') }}">
                            <div class="pull-left"><i class="ti-home"></i><span class="right-nav-text">الرئيسية</span>
                            </div>
                            <div class="clearfix"></div>
                        </a>
                    </li>
                    <!-- menu title -->
                    <li class="mt-10 mb-10 text-muted pl-4 font-medium menu-title">نظام إدارة التحفيظ لمركز الأنصار</li>

                    <li>
                        <a href="javascript:void(0);" data-toggle="collapse"
                           data-target="#lowersupervisors-menu">
                            <div class="pull-left"><i class="fas fa-chalkboard"></i><span
                                    class="right-nav-text">قسم الإدارة العام</span></div>
                            <div class="pull-right"><i class="ti-plus"></i></div>
                            <div class="clearfix"></div>
                        </a>
                        <ul id="lowersupervisors-menu" class="collapse" data-parent="#sidebarnav">
                            @can('قائمة المراحل')
                                <li><a href="{{url('manage_grade')}}">إدارة المراحل</a></li>
                            @endcan
                            @can('قائمة المجموعات')
                                <li><a href="{{url('manage_group')}}">إدارة الحلقات</a></li>
                            @endcan
                            @can('قائمة مشرفي المراحل')
                                <li><a href="{{url('manage_supervisor')}}">إدارة مشرفي المراحل</a></li>
                            @endcan
                            @can('قائمة الإداريين')
                                <li><a href="{{url('manage_lower_supervisor')}}">إدارة إداريي المراحل</a></li>
                            @endcan
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0);" data-toggle="collapse"
                           data-target="#TeachersAttendance-icon">
                            <div class="pull-left"><i class="fas fa-chalkboard-teacher"></i><span
                                    class="right-nav-text">إدارة المحفظين</span></div>
                            <div class="pull-right"><i class="ti-plus"></i></div>
                            <div class="clearfix"></div>
                        </a>
                        <ul id="TeachersAttendance-icon" class="collapse" data-parent="#sidebarnav">
                            @can('قائمة المحفظين')
                                <li><a href="{{url('manage_teacher')}}">إدارة المحفظين</a></li>
                            @endcan
                            @can('قائمة حضور وغياب المحفظين')
                                <li><a href="{{url('manage_teachers_attendance')}}">حضور وغياب المحفظين</a></li>
                            @endcan
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0);" data-toggle="collapse"
                           data-target="#StudentsDailyPreservation-icon">
                            <div class="pull-left"><i class="fas fa-group"></i><span
                                    class="right-nav-text">إدارة الحلقة</span></div>
                            <div class="pull-right"><i class="ti-plus"></i></div>
                            <div class="clearfix"></div>
                        </a>
                        <ul id="StudentsDailyPreservation-icon" class="collapse" data-parent="#sidebarnav">
                            @can('قائمة الطلاب')
                                <li><a href="{{url('manage_student')}}">إدارة الطلاب</a></li>
                            @endcan
                            @can('قائمة حضور وغياب الطلاب')
                                <li><a href="{{url('manage_students_attendance')}}">حضور وغياب الطلاب</a></li>
                            @endcan
                            @can('قائمة متابعة الحفظ والمراجعة')
                                <li><a href="{{url('manage_students_daily_preservation')}}">متابعة الحفظ والمراجعة</a>
                                </li>
                            @endcan
                            @can('قائمة تقرير الحفظ والمراجعة')
                                <li><a href="{{url('manage_report_daily_preservation')}}">تقرير الحفظ والمراجعة</a>
                                </li>
                            @endcan
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#tester_exams">
                            <div class="pull-left"><i class="fas fa-book-open"></i><span
                                    class="right-nav-text">قسم الإختبارات</span></div>
                            <div class="pull-right"><i class="ti-plus"></i></div>
                            <div class="clearfix"></div>
                        </a>
                        <ul id="tester_exams" class="collapse" data-parent="#sidebarnav">
                            @can('قائمة طلبات الإختبارات')
                                <li>
                                    @if (\App\Models\ExamOrder::unreadexams() > 0)
                                        <a href="{{url('manage_exams_orders')}}">طلبات الإختبارات<span
                                                class="badge bg-danger float-right mt-1">{{\App\Models\ExamOrder::unreadexams()}}</span></a>
                                    @else
                                        <a href="{{url('manage_exams_orders')}}">طلبات الإختبارات</a>
                                    @endif
                                </li>
                            @endcan
                            @can('قائمة الإختبارات')
                                <li>
                                    @if (\App\Models\Exam::unreadexams() > 0)
                                        <a href="{{url('manage_exams')}}">الإختبارات القرآنية<span
                                                class="badge bg-danger float-right mt-1">{{\App\Models\Exam::unreadexams()}}</span></a>
                                    @else
                                        <a href="{{url('manage_exams')}}">الإختبارات القرآنية</a>
                                    @endif
                                </li>
                            @endcan
                            @can('قائمة اختبارات اليوم')
                                <li>
                                    @if (\App\Models\ExamOrder::unreadtodayexams() > 0)
                                        <a href="{{url('manage_today_exams')}}">اختبارات اليوم<span
                                                class="badge bg-danger float-right mt-1">{{\App\Models\ExamOrder::unreadtodayexams()}}</span></a>
                                    @else
                                        <a href="{{url('manage_today_exams')}}">اختبارات اليوم</a>
                                    @endif
                                </li>
                            @endcan
                            @can('قائمة المختبرين')
                                <li><a href="{{url('manage_testers')}}">المختبرين</a></li>
                            @endcan
                            @can('قائمة إعدادات الإختبارات')
                                <li><a href="{{url('manage_exams_settings')}}">إعدادات الإختبارات القرآنية</a></li>
                            @endcan
                        </ul>
                    </li>

                @can('قائمة المستخدمين')
                    <!-- Users-->
                        <li>
                            <a href="javascript:void(0);" data-toggle="collapse" data-target="#Users-icon">
                                <div class="pull-left"><i class="fas fa-users"></i><span
                                        class="right-nav-text">المستخدمين</span></div>
                                <div class="pull-right"><i class="ti-plus"></i></div>
                                <div class="clearfix"></div>
                            </a>
                            <ul id="Users-icon" class="collapse" data-parent="#sidebarnav">
                                <li><a href="{{route('roles.index')}}">أدوار المستخدمين</a></li>
                                <li><a href="{{url('manage_users')}}">المستخدمين</a></li>
                            </ul>
                        </li>
                    @endcan
                </ul>
            </div>
        </div>
        <!-- Left Sidebar End-->
