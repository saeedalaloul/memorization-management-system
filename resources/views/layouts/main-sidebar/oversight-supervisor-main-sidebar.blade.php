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
            <a href="{{url('manage_oversight_members',null,true)}}"><i class="fas fa-users"></i><span
                    class="right-nav-text">أعضاء الرقابة</span></a>
        </li>

        <li>
            <a href="{{url('manage_visits_orders',null,true)}}"><i class="fas fa-watch"></i><span
                    class="right-nav-text">طلبات زيارات الرقابة</span></a>
        </li>

        <li>
            <a href="{{url('manage_visits_today',null,true)}}"><i class="fas fa-watch"></i><span
                    class="right-nav-text">طلبات زيارات الرقابة اليوم</span></a>
        </li>

        <li>
            <a href="{{url('manage_visits',null,true)}}"><i class="fas fa-flag"></i><span
                    class="right-nav-text">زيارات الرقابة</span></a>
        </li>

        <li>
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#Managing-endorsement-visit-department">
                <div class="pull-left"><i class="fas fa-envelope"></i><span
                        class="right-nav-text">إقرار زيارة</span></div>
                <div class="pull-right"><i class="ti-plus"></i></div>
                <div class="clearfix"></div>
            </a>
            <ul id="Managing-endorsement-visit-department" class="collapse" data-parent="#sidebarnav">
                <li>
                    <a href="{{url('manage_select_visit_groups',null,true)}}">قسم الحلقات</a>
                </li>
                <li>
                    <a href="{{url('manage_select_visit_testers',null,true)}}">قسم الاختبارات</a>
                </li>
                <li>
                    <a href="{{url('manage_select_visit_activity_members',null,true)}}">قسم الأنشطة</a>
                </li>
                <li>
                    <a href="#">قسم الدورات</a>
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
    </ul>
</div>
