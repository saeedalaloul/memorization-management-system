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
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#Managing-exams-affairs">
                <div class="pull-left"><i class="fas fa-chalkboard-teacher"></i><span
                        class="right-nav-text">إدارة شؤون الإختبارات</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Managing-exams-affairs" class="collapse" data-parent="#sidebarnav">
                @can('إدارة المختبرين')
                    <li><a href="{{url('manage_testers',null,true)}}">المختبرين</a></li>
                @endcan
                <li><a href="{{url('manage_group_exam_statistics',null,true)}}">إحصائية اختبارات الحلقات</a></li>
                <li><a href="{{url('manage_public_exam_statistics',null,true)}}">إحصائية الإختبارات العامة</a></li>
                @can('إضافة اختبار')
                    <li><a href="{{url('manage_add_exam',null,true)}}">إضافة اختبار</a></li>
                @endcan
                @can('إعدادات الإختبارات')
                    <li><a href="{{url('manage_exams_settings',null,true)}}">إعدادات الإختبارات</a></li>
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

                    <li>
                        <a href="{{url('manage_sunnah_exams',null,true)}}">اختبارات السنة</a>
                    </li>
                @endcan
                @can('إدارة الإختبارات الخارجية')
                    <li>
                        <a href="{{url('manage_external_exams',null,true)}}">الإختبارات القرآنية الخارجية</a>
                    </li>
                @endcan
                @can('إدارة اختبارات اليوم')
                    <li>
                        <a href="{{url('manage_today_exams',null,true)}}">اختبارات اليوم</a>
                    </li>
                @endcan
            </ul>
        </li>

        @can('إدارة الحفظة')
            <li>
                <a href="{{url('manage_quran_memorizers',null,true)}}"><i class="fas fa-user-chart"></i><span
                        class="right-nav-text">إدارة الحفظة</span></a>
            </li>
        @endcan
    </ul>
</div>
