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
                        <p class="card-text text-dark">عدد طلبات الزيارات</p>
                        <h4>{{$statistics[0][0]->visit_orders_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                        href="{{url('manage_visits_orders',null,true)}}" target="_blank"><span class="text-danger">عرض البيانات</span></a>
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
                                        <i class="fas fa-envelope highlight-icon" aria-hidden="true"></i>
                                    </span>
                    </div>
                    <div class="float-right text-right">
                        <p class="card-text text-dark">عدد زيارات الشهر</p>
                        <h4>{{$statistics[0][0]->month_visits_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                        href="{{url('manage_visits',null,true)}}" target="_blank"><span
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
                                        <i class="fas fa-envelope highlight-icon" aria-hidden="true"></i>
                                    </span>
                    </div>
                    <div class="float-right text-right">
                        <p class="card-text text-dark">عدد زيارات العام</p>
                        <h4>{{$statistics[0][0]->year_visits_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a href="{{url('manage_visits',null,true)}}"
                                                                                target="_blank"><span
                            class="text-danger">عرض البيانات</span></a>
                </p>
            </div>
        </div>
    </div>
    @if(auth()->user()->current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
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
                            <p class="card-text text-dark">عدد المراقبين</p>
                            <h4>{{$statistics[0][0]->oversight_members_count ?? 0}}</h4>
                        </div>
                    </div>
                    <p class="text-muted pt-3 mb-0 mt-2 border-top">
                        <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                            href="{{url('manage_oversight_members',null,true)}}" target="_blank"><span
                                class="text-danger">عرض البيانات</span></a>
                    </p>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->current_role == \App\Models\User::OVERSIGHT_MEMBER_ROLE)
        <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                                    <span class="text-success">
                                        <i class="fas fa-envelope-open highlight-icon" aria-hidden="true"></i>
                                    </span>
                        </div>
                        <div class="float-right text-right">
                            <p class="card-text text-dark">عدد الزيارات الكلي</p>
                            <h4>{{$statistics[0][0]->visits_count ?? 0}}</h4>
                        </div>
                    </div>
                    <p class="text-muted pt-3 mb-0 mt-2 border-top">
                        <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                            href="{{url('manage_visits',null,true)}}"
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
                                    <a class="nav-link active show" id="visit-orders-tab" data-toggle="tab"
                                       href="#fee_visit_orders"
                                       role="tab" aria-controls="visit-orders" aria-selected="false"> طلبات الزيارات
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="visits-tab" data-toggle="tab" href="#fee-visits"
                                       role="tab" aria-controls="visits" aria-selected="false">زيارات الرقابة
                                    </a>
                                </li>

                                @if(auth()->user()->current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
                                    <li class="nav-item">
                                        <a class="nav-link" id="fee-oversight-members-tab" data-toggle="tab"
                                           href="#fee-oversight-members"
                                           role="tab" aria-controls="fee-oversight-members" aria-selected="false">المراقبين</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content" id="myTabContent">

                        <div class="tab-pane fade active show" id="fee_visit_orders" role="tabpanel"
                             aria-labelledby="visit-orders-tab">
                            <div class="row">
                                <div class="col-xl-12 mb-30">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم المراقب</th>
                                                <th>نوع الزيارة</th>
                                                <th>تاريخ الزيارة</th>
                                                <th>حالة الطلب</th>
                                                <th>تاريخ الإضافة</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($statistics[1] as $visit_order)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ $visit_order->oversight_member_name }}</td>
                                                    <td>
                                                        @if($visit_order->hostable_type == 'App\Models\Teacher')
                                                            زيارة إلى حلقة
                                                        @elseif($visit_order->hostable_type == 'App\Models\Tester')
                                                            زيارة إلى مختبر
                                                        @elseif($visit_order->hostable_type == 'App\Models\ActivityMember')
                                                            زيارة إلى نشاط
                                                        @endif
                                                    </td>
                                                    <td>{{ $visit_order->datetime }}</td>
                                                    <td>
                                                        @if($visit_order->status == \App\Models\VisitOrder::IN_PENDING_STATUS)
                                                            <label class="badge badge-warning">في انتظار الزيارة</label>
                                                        @elseif($visit_order->status == \App\Models\VisitOrder::IN_SENDING_STATUS)
                                                            <label class="badge badge-info">في انتظار الإرسال</label>
                                                        @elseif($visit_order->status == \App\Models\VisitOrder::IN_APPROVAL_STATUS)
                                                            <label class="badge badge-primary">في انتظار الإعتماد</label>
                                                        @endif
                                                    </td>
                                                    <td class="text-success">{{ $visit_order->created_at }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="alert-danger" colspan="6">لاتوجد بيانات</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="fee-visits" role="tabpanel" aria-labelledby="visits-tab">
                            <div class="row">
                                <div class="col-xl-12 mb-30">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم المراقب</th>
                                                <th>نوع الزيارة</th>
                                                <th>تاريخ الزيارة</th>
                                                <th>حالة الزيارة</th>
                                                <th>تاريخ الإضافة</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($statistics[2] as $visit)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ $visit->oversight_member_name }}</td>
                                                    <td>
                                                        @if($visit->hostable_type == 'App\Models\Teacher')
                                                            زيارة إلى حلقة
                                                        @elseif($visit->hostable_type == 'App\Models\Tester')
                                                            زيارة إلى مختبر
                                                        @elseif($visit->hostable_type == 'App\Models\ActivityMember')
                                                            زيارة إلى منشط
                                                        @endif
                                                    </td>
                                                    <td>{{ $visit->datetime }}</td>
                                                    <td>
                                                        @if($visit->status == \App\Models\Visit::IN_PENDING_STATUS)
                                                            <label class="badge badge-warning">مطلوب الرد</label>
                                                        @elseif($visit->status == \App\Models\Visit::REPLIED_STATUS)
                                                            <label class="badge badge-info">تم الرد</label>
                                                        @elseif($visit->status == \App\Models\Visit::IN_PROCESS_STATUS)
                                                            <label class="badge badge-primary">في انتظار المعالجة</label>
                                                        @elseif($visit->status == \App\Models\Visit::FAILURE_STATUS)
                                                            <label class="badge badge-danger">فشل المعالجة</label>
                                                        @elseif($visit->status == \App\Models\Visit::SOLVED_STATUS)
                                                            <label class="badge badge-success">تم الحل</label>
                                                        @endif
                                                    </td>
                                                    <td class="text-success">{{ $visit->created_at }}</td>
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

                        @if(auth()->user()->current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
                            <div class="tab-pane fade" id="fee-oversight-members" role="tabpanel" aria-labelledby="oversight-members-tab">
                                <div class="row">
                                    <div class="col-xl-12 mb-30">
                                        <div class="table-responsive mt-15">
                                            <table style="text-align: center"
                                                   class="table center-aligned-table table-hover mb-0">
                                                <thead>
                                                <tr class="table-info text-danger">
                                                    <th>#</th>
                                                    <th>اسم المراقب</th>
                                                    <th>رقم الهوية</th>
                                                    <th>رقم الجوال</th>
                                                    <th>تاريخ الاضافة</th>
                                                </tr>
                                                </thead>

                                                @forelse($statistics[3] as $oversight_member)
                                                    <tbody>
                                                    <tr>
                                                        <td>{{$loop->iteration}}</td>
                                                        <td>{{$oversight_member->oversight_member_name}}</td>
                                                        <td>{{$oversight_member->identification_number}}</td>
                                                        <td>{{$oversight_member->oversight_member_phone}}</td>
                                                        <td class="text-success">{{$oversight_member->created_at}}</td>
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
