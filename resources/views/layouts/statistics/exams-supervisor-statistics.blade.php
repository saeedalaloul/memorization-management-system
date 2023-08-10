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
                        <p class="card-text text-dark">عدد طلبات الإختبارات</p>
                        <h4>{{$statistics[0][0]->exam_orders_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                        href="{{url('manage_exams_orders',null,true)}}" target="_blank"><span class="text-danger">عرض البيانات</span></a>
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
                                        <i class="fas fa-book-open highlight-icon" aria-hidden="true"></i>
                                    </span>
                    </div>
                    <div class="float-right text-right">
                        <p class="card-text text-dark">عدد اختبارات القرآن خلال الشهر</p>
                        <h4>{{$statistics[0][0]->month_exams_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                        href="{{url('manage_exams',null,true)}}" target="_blank"><span
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
                                    <span class="text-indigo-500">
                                        <i class="fas fa-book-alt highlight-icon" aria-hidden="true"></i>
                                    </span>
                    </div>
                    <div class="float-right text-right">
                        <p class="card-text text-dark">عدد اختبارات السنة خلال الشهر</p>
                        <h4>{{$statistics[0][0]->month_sunnah_exams_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a href="{{url('manage_exams',null,true)}}"
                                                                                target="_blank"><span
                            class="text-danger">عرض البيانات</span></a>
                </p>
            </div>
        </div>
    </div>
    @if(auth()->user()->current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE)
        <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                                    <span class="text-primary">
                                        <i class="fas fa-book-spells highlight-icon" aria-hidden="true"></i>
                                    </span>
                        </div>
                        <div class="float-right text-right">
                            <p class="card-text text-dark">عدد اختبارات القرآن الخارجية لهذا الشهر</p>
                            <h4>{{$statistics[0][0]->external_exams_count ?? 0}}</h4>
                        </div>
                    </div>
                    <p class="text-muted pt-3 mb-0 mt-2 border-top">
                        <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                            href="{{url('manage_exams',null,true)}}" target="_blank"><span
                                class="text-danger">عرض البيانات</span></a>
                    </p>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->current_role == \App\Models\User::TESTER_ROLE)
        <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                                    <span class="text-success">
                                        <i class="fas fa-book-open highlight-icon" aria-hidden="true"></i>
                                    </span>
                        </div>
                        <div class="float-right text-right">
                            <p class="card-text text-dark">عدد الإختبارات الكلي</p>
                            <h4>{{$statistics[0][0]->exams_count ?? 0}}</h4>
                        </div>
                    </div>
                    <p class="text-muted pt-3 mb-0 mt-2 border-top">
                        <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                            href="{{url('manage_exams',null,true)}}"
                            target="_blank"><span
                                class="text-danger">عرض البيانات</span></a>
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

@if(auth()->user()->current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE)
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                                    <span class="text-pinterest">
                                        <i class="fas fa-book highlight-icon" aria-hidden="true"></i>
                                    </span>
                        </div>
                        <div class="float-right text-right">
                            <p class="card-text text-dark">عدد اختبارات السنة الخارجية لهذا الشهر</p>
                            <h4>{{$statistics[0][0]->external_sunnah_exams_count ?? 0}}</h4>
                        </div>
                    </div>
                    <p class="text-muted pt-3 mb-0 mt-2 border-top">
                        <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                            href="{{url('manage_exams',null,true)}}" target="_blank"><span class="text-danger">عرض البيانات</span></a>
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
                                        <i class="fas fa-user highlight-icon" aria-hidden="true"></i>
                                    </span>
                        </div>
                        <div class="float-right text-right">
                            <p class="card-text text-dark">أفضل محفظ خلال الشهر</p>
                            <h6>{{$statistics[0][0]->male_teacher_name_max ?? 'لا يوجد'}}</h6>
                        </div>
                    </div>
                    <p class="text-muted pt-3 mb-0 mt-2 border-top">
                        <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                            href="{{url('manage_group_exam_statistics',null,true)}}"
                            target="_blank"><span
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
                                    <span class="text-danger">
                                        <i class="fas fa-user-alt highlight-icon" aria-hidden="true"></i>
                                    </span>
                        </div>
                        <div class="float-right text-right">
                            <p class="card-text text-dark">أفضل مختبر خلال الشهر</p>
                            <h6>{{$statistics[0][0]->male_tester_name_max ?? 'لا يوجد'}}</h6>
                        </div>
                    </div>
                    <p class="text-muted pt-3 mb-0 mt-2 border-top">
                        <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                            href="{{url('manage_testers',null,true)}}" target="_blank"><span
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
                                        <i class="fas fa-user highlight-icon" aria-hidden="true"></i>
                                    </span>
                        </div>
                        <div class="float-right text-right">
                            <p class="card-text text-dark">أفضل محفظة خلال الشهر</p>
                            <h6>{{$statistics[0][0]->female_teacher_name_max ?? 'لا يوجد'}}</h6>
                        </div>
                    </div>
                    <p class="text-muted pt-3 mb-0 mt-2 border-top">
                        <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                            href="{{url('manage_group_exam_statistics',null,true)}}"
                            target="_blank"><span
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
                                    <span class="text-danger">
                                        <i class="fas fa-user-alt highlight-icon" aria-hidden="true"></i>
                                    </span>
                        </div>
                        <div class="float-right text-right">
                            <p class="card-text text-dark">أفضل مختبرة خلال الشهر</p>
                            <h6>{{$statistics[0][0]->female_tester_name_max ?? 'لا يوجد'}}</h6>
                        </div>
                    </div>
                    <p class="text-muted pt-3 mb-0 mt-2 border-top">
                        <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                            href="{{url('manage_testers',null,true)}}" target="_blank"><span
                                class="text-danger">عرض البيانات</span></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif
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
                                    <a class="nav-link active show" id="exam-orders-tab" data-toggle="tab"
                                       href="#fee_exam_orders"
                                       role="tab" aria-controls="exam-orders" aria-selected="false"> طلبات الإختبارات
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="exams-tab" data-toggle="tab" href="#fee_exams"
                                       role="tab" aria-controls="groups" aria-selected="false">الإختبارات القرآنية
                                    </a>
                                </li>

                                @if(auth()->user()->current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                                    <li class="nav-item">
                                        <a class="nav-link" id="fee_testers-tab" data-toggle="tab"
                                           href="#fee_testers"
                                           role="tab" aria-controls="fee_exams" aria-selected="false">المختبرين</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content" id="myTabContent">

                        <div class="tab-pane fade active show" id="fee_exam_orders" role="tabpanel"
                             aria-labelledby="exam-orders-tab">
                            <div class="row">
                                <div class="col-xl-12 mb-30">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم الطالب</th>
                                                <th>جزء الإختبار</th>
                                                <th>اسم المحفظ</th>
                                                <th>اسم المختبر</th>
                                                <th>حالة الطلب</th>
                                                <th>ملاحظات/تاريخ الإختبار</th>
                                                <th>تاريخ الإضافة</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($statistics[1] as $exam_order)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ $exam_order->student_name }}</td>
                                                    <td>
                                                        @if ($exam_order->type == \App\Models\ExamOrder::IMPROVEMENT_TYPE)
                                                            <label class="badge badge-success">
                                                                @if ($exam_order->quran_part_name != null)
                                                                    {{ $exam_order->quran_part_name . ' (طلب تحسين درجة)' }}
                                                                @else
                                                                    {{ $exam_order->sunnah_part_name . ' (طلب تحسين درجة)' }}
                                                                @endif
                                                            </label>
                                                        @else
                                                            @if ($exam_order->quran_part_name != null)
                                                                {{ $exam_order->quran_part_name }}
                                                            @else
                                                                {{ $exam_order->sunnah_part_name }}
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>{{ $exam_order->teacher_name }}</td>
                                                    <td>{{ $exam_order->tester_name }}</td>
                                                    <td>
                                                        @if($exam_order->status == \App\Models\ExamOrder::IN_PENDING_STATUS)
                                                            <label class="badge badge-warning">قيد الطلب</label>
                                                        @elseif($exam_order->status == \App\Models\ExamOrder::REJECTED_STATUS)
                                                            <label class="badge badge-danger">مرفوض</label>
                                                        @elseif($exam_order->status == \App\Models\ExamOrder::ACCEPTABLE_STATUS)
                                                            <label class="badge badge-success">معتمد</label>
                                                        @elseif($exam_order->status == \App\Models\ExamOrder::FAILURE_STATUS)
                                                            <label class="badge badge-danger">لم يختبر</label>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($exam_order->status == \App\Models\ExamOrder::ACCEPTABLE_STATUS)
                                                            {{ \Carbon\Carbon::parse($exam_order->datetime)->format('Y-m-d') }}
                                                        @else
                                                            {{ $exam_order->notes }}
                                                        @endif
                                                    </td>
                                                    <td class="text-success">{{ $exam_order->created_at }}</td>
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

                        <div class="tab-pane fade" id="fee_exams" role="tabpanel" aria-labelledby="exams-tab">
                            <div class="row">
                                <div class="col-xl-12 mb-30">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم الطالب</th>
                                                <th>جزء الإختبار</th>
                                                <th>درجة الإختبار</th>
                                                <th>اسم المحفظ</th>
                                                <th>اسم المختبر</th>
                                                <th>تاريخ الإضافة</th>
                                                <th>ملاحظات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($statistics[2] as $exam)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ $exam->student_name }}</td>
                                                    <td>{{ $exam->quran_part_name}}</td>
                                                    <td style="text-align: center; align-content: center">
                                                        @if ($exam->mark >= $exam->exam_success_mark)
                                                            <div class="badge-success" style="width: 40px;">
                                                                {{ $exam->mark.'%' }}
                                                            </div>
                                                        @else
                                                            <div class="badge-danger" style="width: 40px;">
                                                                {{ $exam->mark.'%' }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $exam->teacher_name }}</td>
                                                    <td>{{ $exam->tester_name }}</td>
                                                    <td class="text-success">{{ $exam->datetime }}</td>
                                                    <td>{{ $exam->notes }}</td>
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

                        @if(auth()->user()->current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                            <div class="tab-pane fade" id="fee_testers" role="tabpanel" aria-labelledby="testers-tab">
                                <div class="row">
                                    <div class="col-xl-12 mb-30">
                                        <div class="table-responsive mt-15">
                                            <table style="text-align: center"
                                                   class="table center-aligned-table table-hover mb-0">
                                                <thead>
                                                <tr class="table-info text-danger">
                                                    <th>#</th>
                                                    <th>اسم المختبر</th>
                                                    <th>رقم الهوية</th>
                                                    <th>رقم الجوال</th>
                                                    <th>تاريخ الاضافة</th>
                                                </tr>
                                                </thead>

                                                @forelse($statistics[3] as $tester)
                                                    <tbody>
                                                    <tr>
                                                        <td>{{$loop->iteration}}</td>
                                                        <td>{{$tester->tester_name}}</td>
                                                        <td>{{$tester->tester_identification_number}}</td>
                                                        <td>{{$tester->tester_phone}}</td>
                                                        <td class="text-success">{{$tester->created_at}}</td>
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
