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
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#General-administration-department">
                <div class="pull-left"><i class="fas fa-chalkboard"></i><span
                        class="right-nav-text">قسم الإدارة العام</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="General-administration-department" class="collapse" data-parent="#sidebarnav">
                @can('إدارة المراحل')
                    <li><a href="{{url('manage_grade',null,true)}}">إدارة المراحل</a></li>
                @endcan
                @can('إدارة المجموعات')
                    <li><a href="{{url('manage_group',null,true)}}">إدارة الحلقات</a></li>
                @endcan
                @can('إدارة مشرفي المراحل')
                    <li><a href="{{url('manage_supervisor',null,true)}}">إدارة مشرفي المراحل</a></li>
                @endcan
                @can('إدارة الإداريين')
                    <li><a href="{{url('manage_lower_supervisor',null,true)}}">إدارة إداريي المراحل</a></li>
                @endcan
                @can('إدارة المحفظين')
                    <li><a href="{{url('manage_teacher',null,true)}}">إدارة المحفظين</a></li>@endcan
                @can('إدارة الطلاب')
                    <li><a href="{{url('manage_student',null,true)}}">إدارة الطلاب</a></li>
                @endcan
            </ul>
        </li>

        <li>
            <a href="javascript:void(0);" data-toggle="collapse"
               data-target="#Group-affairs-management">
                <div class="pull-left"><i class="fas fa-group"></i><span
                        class="right-nav-text">إدارة شؤون الحلقة</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Group-affairs-management" class="collapse" data-parent="#sidebarnav">
                @can('إدارة متابعة الحفظ والمراجعة')
                    <li><a href="{{url('manage_students_daily_preservation',null,true)}}">متابعة الحفظ
                            والمراجعة</a>
                    </li>
                @endcan
                @can('إدارة تقرير الحفظ والمراجعة')
                    <li><a href="{{url('manage_report_daily_preservation',null,true)}}">تقرير الحفظ
                            والمراجعة</a>
                    </li>
                @endcan
            </ul>
        </li>

        <li>
            <a href="javascript:void(0);" data-toggle="collapse"
               data-target="#Complaints-suggestions-fund-management">
                <div class="pull-left"><i class="fas fa-group"></i><span
                        class="right-nav-text">إدارة صندوق الشكاوي</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Complaints-suggestions-fund-management" class="collapse" data-parent="#sidebarnav">
                @can('إدارة صندوق الشكاوي والإقتراحات')
                    <li><a href="{{url('manage_box_complaint_suggestions',null,true)}}">صندوق الشكاوي والإقتراحات</a>
                    </li>
                @endcan
                @can('إدارة تصنيفات صندوق الشكاوي والإقتراحات')
                    <li><a href="{{url('manage_complaint_box_categories',null,true)}}">تصنيفات صندوق الشكاوي والإقتراحات</a>
                    </li>
                @endcan
                    @can('إدارة أدوار صندوق الشكاوي والإقتراحات')
                        <li><a href="{{url('manage_complaint_box_roles',null,true)}}">أدوار صندوق الشكاوي والإقتراحات</a>
                        </li>
                    @endcan
            </ul>
        </li>

        <li>
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#Managing-individual-exams-department">
                <div class="pull-left"><i class="fas fa-book-open"></i><span
                        class="right-nav-text">إدارة الإختبارات المنفردة</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Managing-individual-exams-department" class="collapse" data-parent="#sidebarnav">
                @can('إدارة طلبات الإختبارات')
                    <li>
                        @if (\App\Models\ExamOrder::unreadexams() > 0)
                            <a href="{{url('manage_exams_orders',null,true)}}">طلبات الإختبارات<span
                                    class="badge bg-danger float-right mt-1">{{\App\Models\ExamOrder::unreadexams()}}</span></a>
                        @else
                            <a href="{{url('manage_exams_orders',null,true)}}">طلبات الإختبارات</a>
                        @endif
                    </li>
                @endcan
                @can('إدارة الإختبارات')
                    <li>
                        @if (\App\Models\Exam::unreadexams() > 0)
                            <a href="{{url('manage_exams',null,true)}}">الإختبارات القرآنية<span
                                    class="badge bg-danger float-right mt-1">{{\App\Models\Exam::unreadexams()}}</span></a>
                        @else
                            <a href="{{url('manage_exams',null,true)}}">الإختبارات القرآنية</a>
                        @endif
                    </li>
                @endcan
                @can('إدارة اختبارات اليوم')
                    <li>
                        @if (\App\Models\ExamOrder::unreadtodayexams() > 0)
                            <a href="{{url('manage_today_exams',null,true)}}">اختبارات اليوم<span
                                    class="badge bg-danger float-right mt-1">{{\App\Models\ExamOrder::unreadtodayexams()}}</span></a>
                        @else
                            <a href="{{url('manage_today_exams',null,true)}}">اختبارات اليوم</a>
                        @endif
                    </li>
                @endcan
            </ul>
        </li>

        <li>
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#Managing-summative-exams-department">
                <div class="pull-left"><i class="fas fa-book"></i><span
                        class="right-nav-text">إدارة اختبارات التجميعي</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Managing-summative-exams-department" class="collapse" data-parent="#sidebarnav">
                @can('إدارة طلبات اختبارات التجميعي')
                    <li>
                        @if (\App\Models\ExamSummativeOrder::unreadexams() > 0)
                            <a href="{{url('manage_exams_summative_orders',null,true)}}">طلبات الإختبارات<span
                                    class="badge bg-danger float-right mt-1">{{\App\Models\ExamSummativeOrder::unreadexams()}}</span></a>
                        @else
                            <a href="{{url('manage_exams_summative_orders',null,true)}}">طلبات الإختبارات</a>
                        @endif
                    </li>
                @endcan
                @can('إدارة اختبارات التجميعي')
                    <li>
                        @if (\App\Models\SummativeExam::unreadexams() > 0)
                            <a href="{{url('manage_exams_summative',null,true)}}">الإختبارات القرآنية<span
                                    class="badge bg-danger float-right mt-1">{{\App\Models\SummativeExam::unreadexams()}}</span></a>
                        @else
                            <a href="{{url('manage_exams_summative',null,true)}}">الإختبارات القرآنية</a>
                        @endif
                    </li>
                @endcan
                @can('إدارة اختبارات التجميعي اليوم')
                    <li>
                        @if (\App\Models\ExamSummativeOrder::unreadtodayexams() > 0)
                            <a href="{{url('manage_today_exams_summative',null,true)}}">اختبارات اليوم<span
                                    class="badge bg-danger float-right mt-1">{{\App\Models\ExamSummativeOrder::unreadtodayexams()}}</span></a>
                        @else
                            <a href="{{url('manage_today_exams_summative',null,true)}}">اختبارات اليوم</a>
                        @endif
                    </li>
                @endcan
            </ul>
        </li>

        <li>
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#Managing-exams-department">
                <div class="pull-left"><i class="fas fa-chalkboard-teacher"></i><span
                        class="right-nav-text">إدارة شؤون الإختبارات</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Managing-exams-department" class="collapse" data-parent="#sidebarnav">
                @can('إدارة المختبرين')
                    <li><a href="{{url('manage_testers',null,true)}}">المختبرين</a></li>
                @endcan
                @can('إعدادات الإختبارات')
                    <li><a href="{{url('manage_exams_settings',null,true)}}">إعدادات الإختبارات القرآنية</a></li>
                @endcan
            </ul>
        </li>


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
                @can('إدارة المستخدمين')
                    <li><a href="{{url('manage_users',null,true)}}">المستخدمين</a></li>
                @endcan
            </ul>
        </li>

        <!-- Settings-->
        <li>
            <a href="{{url('manage_settings',null,true)}}"><i class="fas fa-cogs"></i><span
                    class="right-nav-text">الإعدادات</span></a>
        </li>

    </ul>
</div>
