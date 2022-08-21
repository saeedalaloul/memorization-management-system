<!-- widgets -->
<div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                                    <span class="text-success">
                                        <i class="fas fa-user-graduate highlight-icon" aria-hidden="true"></i>
                                    </span>
                    </div>
                    <div class="float-right text-right">
                        <p class="card-text text-dark">عدد الطلاب</p>
                        <h4>{{$statistics[0][0]->students_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                        href="{{url('manage_student',null,true)}}" target="_blank"><span class="text-danger">عرض البيانات</span></a>
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
                                        <i class="fas fa-chalkboard-teacher highlight-icon" aria-hidden="true"></i>
                                    </span>
                    </div>
                    <div class="float-right text-right">
                        <p class="card-text text-dark">عدد المحفظين</p>
                        <h4>{{$statistics[0][0]->teachers_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a
                        href="{{url('manage_teacher',null,true)}}" target="_blank"><span class="text-danger">عرض البيانات</span></a>
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
                                        <i class="fas fa-group highlight-icon" aria-hidden="true"></i>
                                    </span>
                    </div>
                    <div class="float-right text-right">
                        <p class="card-text text-dark">عدد الحلقات</p>
                        <h4>{{$statistics[0][0]->groups_count ?? 0}}</h4>
                    </div>
                </div>
                <p class="text-muted pt-3 mb-0 mt-2 border-top">
                    <i class="fas fa-binoculars mr-1" aria-hidden="true"></i><a href="{{url('manage_group',null,true)}}"
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
                                    <span class="text-primary">
                                        <i class="fas fa-book-reader highlight-icon" aria-hidden="true"></i>
                                    </span>
                    </div>
                    <div class="float-right text-right">
                        <p class="card-text text-dark">عدد الإختبارات القرآنية</p>
                        <h4>{{$statistics[0][0]->exams_count ?? 0}}</h4>
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
                                    <a class="nav-link active show" id="students-tab" data-toggle="tab"
                                       href="#fee_students" role="tab" aria-controls="students"
                                       aria-selected="true"> الطلاب</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="teachers-tab" data-toggle="tab" href="#fee_teachers"
                                       role="tab" aria-controls="teachers" aria-selected="false">المحفظين
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="groups-tab" data-toggle="tab" href="#fee_groups"
                                       role="tab" aria-controls="groups" aria-selected="false">الحلقات
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="fee_exams-tab" data-toggle="tab"
                                       href="#fee_exams"
                                       role="tab" aria-controls="fee_exams" aria-selected="false">الإختبارات
                                        القرآنية
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </div>

                    <div class="tab-content" id="myTabContent">

{{--                        students Table--}}
                        <div class="tab-pane fade active show" id="fee_students" role="tabpanel"
                             aria-labelledby="students-tab">
                            <div class="row">
                                <div class="col-xl-12 mb-30">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم الطالب</th>
                                                <th>رقم الهوية</th>
                                                <th>اسم المرحلة</th>
                                                <th>اسم الحلقة</th>
                                                <th>اسم المحفظ</th>
                                                <th>تاريخ الاضافة</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($statistics[1] as $student)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$student->student_name}}</td>
                                                    <td>{{$student->student_identification_number}}</td>
                                                    <td>{{$student->grade_name}}</td>
                                                    <td>{{$student->group_name}}</td>
                                                    <td>{{$student->teacher_name}}</td>
                                                    <td class="text-success">{{$student->created_at}}</td>
                                                    @empty
                                                        <td class="alert-danger" colspan="8">لاتوجد بيانات</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

{{--                        teachers Table--}}
                        <div class="tab-pane fade" id="fee_teachers" role="tabpanel" aria-labelledby="teachers-tab">
                            <div class="row">
                                <div class="col-xl-12 mb-30">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم المحفظ</th>
                                                <th>رقم الهوية</th>
                                                <th>اسم المرحلة</th>
                                                <th>رقم الجوال</th>
                                                <th>تاريخ الاضافة</th>
                                            </tr>
                                            </thead>

                                            @forelse($statistics[2] as $teacher)
                                                <tbody>
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$teacher->teacher_name}}</td>
                                                    <td>{{$teacher->teacher_identification_number}}</td>
                                                    <td>{{$teacher->grade_name}}</td>
                                                    <td>{{$teacher->teacher_phone}}</td>
                                                    <td class="text-success">{{$teacher->created_at}}</td>
                                                    @empty
                                                        <td class="alert-danger" colspan="8">لاتوجد بيانات</td>
                                                </tr>
                                                </tbody>
                                            @endforelse
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
{{--                        sections Table--}}
                        <div class="tab-pane fade" id="fee_groups" role="tabpanel"
                             aria-labelledby="fee_groups-tab">
                            <div class="row">
                                <div class="col-xl-12 mb-30">
                                    <div class="table-responsive mt-15">
                                        <table style="text-align: center"
                                               class="table center-aligned-table table-hover mb-0">
                                            <thead>
                                            <tr class="table-info text-danger">
                                                <th>#</th>
                                                <th>اسم الحلقة</th>
                                                <th>اسم المرحلة</th>
                                                <th>اسم المحفظ</th>
                                                <th>عدد طلاب الحلقة</th>
                                                <th>تاريخ الإضافة</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($statistics[3] as $group)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ $group->group_name }}</td>
                                                    <td>{{ $group->grade_name}}</td>
                                                    <td>{{ $group->teacher_name }}</td>
                                                    <td>{{ $group->students_count }}</td>
                                                    <td class="text-success">{{ $group->created_at }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="alert-danger" colspan="9">لاتوجد بيانات</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--                        parents Table--}}
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
                                            @forelse($statistics[4] as $exam)
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
                                                    <td class="alert-danger" colspan="9">لاتوجد بيانات</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
