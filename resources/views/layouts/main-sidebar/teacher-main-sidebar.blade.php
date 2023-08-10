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
               data-target="#Group-affairs-management">
                <div class="pull-left"><i class="fas fa-group"></i><span
                        class="right-nav-text">إدارة شؤون الحلقة</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Group-affairs-management" class="collapse" data-parent="#sidebarnav">
                @can('إدارة الطلاب')
                    <li><a href="{{url('manage_student',null,true)}}">إدارة الطلاب</a></li>
                @endcan
                @can('إدارة متابعة الحفظ والمراجعة')
                    <li><a href="{{url('manage_students_daily_memorization',null,true)}}">متابعة الحفظ والمراجعة</a>
                    </li>
                @endcan
                @can('إدارة تقرير الحفظ والمراجعة')
                    @php
                        $group = \App\Models\Group::where('teacher_id',auth()->id())->first();
                    @endphp
                    @if ($group !== null && $group->type === \App\Models\Group::SUNNAH_TYPE)
                        <li><a href="{{url('manage_report_daily_memorization_sunnah',null,true)}}">تقرير الحفظ
                                والمراجعة</a></li>
                    @else
                        <li><a href="{{url('manage_report_daily_memorization',null,true)}}">تقرير الحفظ والمراجعة</a>
                        </li>
                    @endif
                @endcan
                @can('إدارة الحفظة')
                    <li><a href="{{url('manage_quran_memorizers',null,true)}}">إدارة الحفظة</a>
                    </li>
                @endcan
                <li><a href="{{url('manage_track_student_transfers',null,true)}}">إدارة تتبع تنقلات الطلاب</a></li>
                @can('إدارة التقارير الشهرية')
                    <li><a href="{{url('manage_report_monthly_memorization',null,true)}}">التقارير الشهرية</a>
                    </li>
                @endcan
            </ul>
        </li>

        <li>
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#Managing-exams-department">
                <div class="pull-left"><i class="fas fa-book-open"></i><span
                        class="right-nav-text">إدارة الإختبارات</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Managing-exams-department" class="collapse" data-parent="#sidebarnav">
                @can('إدارة طلبات الإختبارات')
                    <li>
                        <a href="{{url('manage_exams_orders',null,true)}}">طلبات الإختبارات</a>
                    </li>
                @endcan
                @can('إدارة الإختبارات')
                    @if ($group !== null && $group->type === \App\Models\Group::SUNNAH_TYPE)
                        <li>
                            <a href="{{url('manage_sunnah_exams',null,true)}}">اختبارات السنة</a>
                        </li>
                    @else
                        <li>
                            <a href="{{url('manage_exams',null,true)}}">الإختبارات القرآنية</a>
                        </li>
                    @endif
                @endcan
                @can('إدارة اختبارات اليوم')
                    <li>
                        <a href="{{url('manage_today_exams',null,true)}}">اختبارات اليوم</a>
                    </li>
                @endcan
            </ul>
        </li>

        <li>
            <a href="javascript:void(0);" data-toggle="collapse"
               data-target="#Activities-management">
                <div class="pull-left"><i class="fas fa-flag"></i><span
                        class="right-nav-text">إدارة الأنشطة</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>

            <ul id="Activities-management" class="collapse" data-parent="#sidebarnav">
                @can('إدارة طلبات الأنشطة')
                    <li>
                        <a href="{{url('manage_activities_orders',null,true)}}"><span
                                class="right-nav-text">طلبات الأنشطة</span></a>
                    </li>
                @endcan

                @can('إدارة الأنشطة')
                    <li>
                        <a href="{{url('manage_activities',null,true)}}">إدارة الأنشطة</a>
                    </li>
                @endcan
            </ul>
        </li>

        <li>
            <a href="javascript:void(0);" data-toggle="collapse"
               data-target="#Complaints-suggestions-fund-management">
                <div class="pull-left"><i class="fas fa-support"></i><span
                        class="right-nav-text">إدارة صندوق الشكاوي</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Complaints-suggestions-fund-management" class="collapse" data-parent="#sidebarnav">
                @can('إدارة صندوق الشكاوي والإقتراحات')
                    <li>
                        <a href="{{url('manage_box_complaint_suggestions',null,true)}}">صندوق الشكاوي
                            والإقتراحات</a>
                    </li>
                @endcan
            </ul>
        </li>

    </ul>
</div>
