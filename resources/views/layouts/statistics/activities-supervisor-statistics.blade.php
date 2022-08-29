<!-- widgets -->
<div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                                    <span class="text-success">
                                        <i class="fas fa-watch highlight-icon" aria-hidden="true"></i>
                                    </span>
                    </div>
                    <div class="float-right text-right">
                        <p class="card-text text-dark">عدد طلبات الأنشطة</p>
                        <h4>{{$statistics[0][0]->activity_orders_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                        href="{{url('manage_activities_orders',null,true)}}" target="_blank"><span class="text-danger">عرض البيانات</span></a>
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                                    <span class="text-warning">
                                        <i class="fas fa-flag-checkered highlight-icon" aria-hidden="true"></i>
                                    </span>
                    </div>
                    <div class="float-right text-right">
                        <p class="card-text text-dark">عدد أنشطة الشهر</p>
                        <h4>{{$statistics[0][0]->month_activities_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                        href="{{url('manage_activities',null,true)}}" target="_blank"><span
                            class="text-danger">عرض البيانات</span></a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                                    <span class="text-success">
                                        <i class="fas fa-flag-checkered highlight-icon" aria-hidden="true"></i>
                                    </span>
                    </div>
                    <div class="float-right text-right">
                        <p class="card-text text-dark">عدد أنشطة العام</p>
                        <h4>{{$statistics[0][0]->year_activities_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                        href="{{url('manage_activities',null,true)}}"
                        target="_blank"><span
                            class="text-danger">عرض البيانات</span></a>
                </p>
            </div>
        </div>
    </div>
    @if(auth()->user()->current_role == \App\Models\User::ACTIVITIES_SUPERVISOR_ROLE)
        <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                                    <span class="text-primary">
                                        <i class="fas fa-chalkboard-teacher highlight-icon" aria-hidden="true"></i>
                                    </span>
                        </div>
                        <div class="float-right text-right">
                            <p class="card-text text-dark">عدد المنشطين</p>
                            <h4>{{$statistics[0][0]->activity_members_count ?? 0}}</h4>
                        </div>
                    </div>
                    <p class="text-muted pt-3 mb-0 mt-2 border-top">
                        <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                            href="{{url('manage_activity_members',null,true)}}" target="_blank"><span
                                class="text-danger">عرض البيانات</span></a>
                    </p>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->current_role == \App\Models\User::ACTIVITY_MEMBER_ROLE)
        <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                                    <span class="text-success">
                                        <i class="fas fa-flag-checkered highlight-icon" aria-hidden="true"></i>
                                    </span>
                        </div>
                        <div class="float-right text-right">
                            <p class="card-text text-dark">عدد الأنشطة الكلي</p>
                            <h4>{{$statistics[0][0]->activities_count ?? 0}}</h4>
                        </div>
                    </div>
                    <p class="text-muted pt-3 mb-0 mt-2 border-top">
                        <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                            href="{{url('manage_activities',null,true)}}"
                            target="_blank"><span
                                class="text-danger">عرض البيانات</span></a>
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
<!-- Orders Status widgets-->

<div class="row">
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="tab nav-border" style="position: relative;">
                    <div class="d-block d-md-flex justify-content-between">
                        <div class="d-block w-100">
                            <h5 style="font-family: 'Cairo', sans-serif" class="card-title">اخر العمليات علي
                                النظام</h5>
                        </div>
                        <div class="d-block d-md-flex nav-tabs-custom">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">

                                <li class="nav-item">
                                    <a class="nav-link active show" id="activity-orders-tab" data-toggle="tab"
                                       href="#fee_activity_orders"
                                       role="tab" aria-controls="activity-orders" aria-selected="false"> طلبات الأنشطة
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="activities-tab" data-toggle="tab" href="#fee-activities"
                                       role="tab" aria-controls="activities" aria-selected="false">الأنشطة
                                    </a>
                                </li>

                                @if(auth()->user()->current_role == \App\Models\User::ACTIVITIES_SUPERVISOR_ROLE)
                                    <li class="nav-item">
                                        <a class="nav-link" id="fee-activity-members-tab" data-toggle="tab"
                                           href="#fee-activity-members"
                                           role="tab" aria-controls="fee-activity-members" aria-selected="false">المنشطين</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content" id="myTabContent">

                        <div class="tab-pane fade active show" id="fee_activity_orders" role="tabpanel"
                             aria-labelledby="activity-orders-tab">
                            <div class="row">
                                <div class="col-xl-12 mb-30">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم النشاط</th>
                                                <th>عدد الطلاب</th>
                                                <th>الوقت</th>
                                                <th>اسم المحفظ</th>
                                                <th>اسم المنشط</th>
                                                <th>الحالة</th>
                                                <th>تاريخ الإضافة</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($statistics[1] as $activity_order)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ $activity_order->activity_name }}</td>
                                                    <td>{{ $activity_order->students_activity_count }}</td>
                                                    <td>{{ $activity_order->datetime }}</td>
                                                    <td>{{ $activity_order->activity_teacher_name }}</td>
                                                    <td>{{ $activity_order->activity_member_name }}</td>
                                                    <td>
                                                        @if($activity_order->status == \App\Models\ActivityOrder::IN_PENDING_STATUS)
                                                            <label class="badge badge-primary">قيد الدراسة</label>
                                                        @elseif($activity_order->status == \App\Models\ActivityOrder::ACCEPTABLE_STATUS)
                                                            <label class="badge badge-success">معتمد</label>
                                                        @elseif($activity_order->status == \App\Models\ActivityOrder::REJECTED_STATUS)
                                                            <label class="badge badge-danger">مرفوض</label>
                                                        @elseif($activity_order->status == \App\Models\ActivityOrder::FAILURE_STATUS)
                                                            <label class="badge badge-warning">فشل إجراء النشاط</label>
                                                        @endif
                                                    </td>
                                                    <td class="text-success">{{ $activity_order->created_at }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="alert-danger" colspan="8">لاتوجد بيانات</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="fee-activities" role="tabpanel" aria-labelledby="activities-tab">
                            <div class="row">
                                <div class="col-xl-12 mb-30">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم النشاط</th>
                                                <th>عدد الطلاب</th>
                                                <th>الوقت</th>
                                                <th>اسم المحفظ</th>
                                                <th>اسم المنشط</th>
                                                <th>تاريخ الإضافة</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($statistics[2] as $activity)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ $activity->activity_name }}</td>
                                                    <td>{{ $activity->students_activity_count }}</td>
                                                    <td>{{ $activity->datetime }}</td>
                                                    <td>{{ $activity->activity_teacher_name }}</td>
                                                    <td>{{ $activity->activity_member_name }}</td>
                                                    <td class="text-success">{{ $activity->created_at }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="alert-danger" colspan="7">لاتوجد بيانات</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(auth()->user()->current_role == \App\Models\User::ACTIVITIES_SUPERVISOR_ROLE)
                            <div class="tab-pane fade" id="fee-activity-members" role="tabpanel"
                                 aria-labelledby="activity-members-tab">
                                <div class="row">
                                    <div class="col-xl-12 mb-30">
                                        <div class="table-responsive mt-15">
                                            <table style="text-align: center"
                                                   class="table center-aligned-table table-hover mb-0">
                                                <thead>
                                                <tr class="table-info text-danger">
                                                    <th>#</th>
                                                    <th>اسم المنشط</th>
                                                    <th>رقم الهوية</th>
                                                    <th>رقم الجوال</th>
                                                    <th>تاريخ الاضافة</th>
                                                </tr>
                                                </thead>

                                                @forelse($statistics[3] as $activity_member)
                                                    <tbody>
                                                    <tr>
                                                        <td>{{$loop->iteration}}</td>
                                                        <td>{{$activity_member->activity_member_name}}</td>
                                                        <td>{{$activity_member->identification_number}}</td>
                                                        <td>{{$activity_member->activity_member_phone}}</td>
                                                        <td class="text-success">{{$activity_member->created_at}}</td>
                                                        @empty
                                                            <td class="alert-danger" colspan="3">لاتوجد بيانات</td>
                                                    </tr>
                                                    </tbody>
                                                @endforelse
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
