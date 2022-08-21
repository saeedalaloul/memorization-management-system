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
                @can('إدارة المحفظين')
                    <li><a href="{{url('manage_teacher',null,true)}}">إدارة المحفظين</a></li>@endcan
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
                @can('إدارة الطلاب')
                    <li><a href="{{url('manage_student',null,true)}}">إدارة الطلاب</a></li>
                @endcan
                @can('إدارة تقرير الحفظ والمراجعة')
                    <li><a href="{{url('manage_report_daily_memorization',null,true)}}">تقرير الحفظ
                            والمراجعة</a>
                    </li>
                @endcan
                @can('إدارة التقارير الشهرية')
                    <li><a href="{{url('manage_report_monthly_memorization',null,true)}}">التقارير الشهرية</a>
                    </li>
                @endcan
                @can('إدارة الاجراءات العقابية')
                    <li><a href="{{url('manage_punitive_measures',null,true)}}">إدارة الاجراءات العقابية</a></li>
                @endcan
            </ul>
        </li>


        <li>
            <a href="javascript:void(0);" data-toggle="collapse"
               data-target="#Managing-activities-department">
                <div class="pull-left"><i class="fas fa-flag"></i><span
                        class="right-nav-text">إدارة الأنشطة</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Managing-activities-department" class="collapse" data-parent="#sidebarnav">
                @can('إدارة الأنشطة')
                    <li>
                        <a href="{{url('manage_activities',null,true)}}">إدارة الأنشطة</a>
                    </li>
                @endcan
            </ul>
        </li>

        <li>
            <a href="javascript:void(0);" data-toggle="collapse"
               data-target="#Managing-visits-department">
                <div class="pull-left"><i class="fas fa-envelope"></i><span
                        class="right-nav-text">إدارة زيارات الرقابة</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Managing-visits-department" class="collapse" data-parent="#sidebarnav">
                <li>
                    <a href="{{url('manage_visits',null,true)}}">إدارة زيارات الرقابة</a>
                </li>
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

        <li>
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#Managing-exams-department">
                <div class="pull-left"><i class="fas fa-book-open"></i><span
                        class="right-nav-text">إدارة الإختبارات القرآنية</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Managing-exams-department" class="collapse" data-parent="#sidebarnav">
                @can('إدارة الإختبارات')
                    <li>
                        <a href="{{url('manage_exams',null,true)}}">الإختبارات القرآنية</a>
                    </li>
                @endcan
            </ul>
        </li>

        <!-- Users-->
        <li>
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#Users-icon">
                <div class="pull-left"><i class="fas fa-users"></i><span
                        class="right-nav-text">إدارة المستخدمين</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Users-icon" class="collapse" data-parent="#sidebarnav">
                @can('إدارة الأدوار')
                    <li><a href="{{url('manage_roles',null,true)}}">إدارة أدوار المستخدمين</a></li>
                @endcan
                @can('إدارة المستخدمين')
                    <li><a href="{{url('manage_users',null,true)}}">إدارة المستخدمين</a></li>
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
