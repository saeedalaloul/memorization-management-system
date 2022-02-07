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

    </ul>
</div>
