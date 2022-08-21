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

    </ul>
</div>
