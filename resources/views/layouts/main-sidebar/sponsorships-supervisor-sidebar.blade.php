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
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#Grade-administration-department">
                <div class="pull-left"><i class="fas fa-chalkboard"></i><span
                        class="right-nav-text">قسم الإدارة العام</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Grade-administration-department" class="collapse" data-parent="#sidebarnav">
                @can('إدارة المجموعات')
                    <li><a href="{{url('manage_group',null,true)}}">إدارة الحلقات</a></li>
                @endcan
                @can('إدارة المحفظين')
                    <li><a href="{{url('manage_teacher',null,true)}}">إدارة المحفظين</a></li>
                @endcan
                @can('إدارة الطلاب')
                    <li><a href="{{url('manage_student',null,true)}}">إدارة الطلاب</a></li>
                @endcan
            </ul>
        </li>

        <li>
            <a href="javascript:void(0);" data-toggle="collapse"
               data-target="#Group-affairs-management">
                <div class="pull-left"><i class="fas fa-group"></i><span
                        class="right-nav-text">إدارة شؤون الحلقات</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Group-affairs-management" class="collapse" data-parent="#sidebarnav">
                @can('إدارة تقرير الحفظ والمراجعة')
                    <li><a href="{{url('manage_report_daily_memorization',null,true)}}">تقرير الحفظ
                            والمراجعة قسم (القرآن)</a>
                    </li>
                @endcan
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
                    <li>
                        <a href="{{url('manage_exams',null,true)}}">الإختبارات القرآنية</a>
                    </li>
                @endcan
                @can('إدارة الإختبارات الخارجية')
                    <li>
                        <a href="{{url('manage_external_exams',null,true)}}">الإختبارات القرآنية الخارجية</a>
                    </li>
                @endcan
            </ul>
        </li>

    </ul>
</div>
