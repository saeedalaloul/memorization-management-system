<li class="nav-item dropdown">
    <a class="nav-link top-nav" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
       aria-expanded="true"> <i class=" ti-view-grid"></i> </a>
    <div class="dropdown-menu dropdown-menu-right dropdown-big">
        <div class="dropdown-header">
            <strong>راوبط سريعة</strong>
        </div>
        <div class="dropdown-divider"></div>
        <div class="nav-grid">
            <a href="{{route('manage_student')}}" class="nav-grid-item"><i class="ti-user text-primary"></i><h5>إدارة الطلاب</h5></a>
            <a href="{{route('manage_report_monthly_memorization')}}" class="nav-grid-item"><i class="ti-window text-primary"></i><h5>إدارة التقارير الشهرية</h5></a>
        </div>
        <div class="nav-grid">
            <a href="{{route('manage_group')}}" class="nav-grid-item"><i class="ti-check text-warning"></i><h5>إدارة الحلقات</h5>
            </a>
            <a href="{{route('manage_report_daily_memorization')}}" class="nav-grid-item"><i class="ti-bookmark text-danger"></i><h5>تقرير الحفظ والمراجعة</h5></a>
        </div>
    </div>
</li>
