<div class="col-xl-12 mb-30">
    @php
        $student =  $this->student[0];
        $exams =  $this->student_exams;
    @endphp
    <div class="card card-statistics h-100">
        <div class="card-body">
            <h5 class="card-title"> عرض بيانات الطالب</h5>
            <div class="tab round shadow" x-data="{currentStudentTab: $persist('profile')}">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" @click.prevent="currentStudentTab = 'profile'">
                        <a class="nav-link" :class="currentStudentTab == 'profile' ? 'active show':'' "
                           id="profile-08-tab"
                           href="#profile-08" role="tab" aria-controls="profile-08" aria-selected="true"> <i
                                class="fa fa-user"></i> البيانات
                            الشخصية</a>
                    </li>
                    <li class="nav-item" @click.prevent="currentStudentTab = 'academic'">
                        <a class="nav-link" :class="currentStudentTab == 'academic' ? 'active show':'' "
                           id="academic-08-tab"
                           href="#academic-08" role="tab" aria-controls="academic-08" aria-selected="false"><i
                                class="fas fa-group"></i> بيانات الحفظ
                        </a>
                    </li>
                    <li class="nav-item" @click.prevent="currentStudentTab = 'exams'">
                        <a class="nav-link" :class="currentStudentTab == 'exams' ? 'active show':'' " id="exams-08-tab"
                           href="#exams-08" role="tab" aria-controls="exams-08" aria-selected="false"><i
                                class="fas fa-book-open"></i>
                            الإختبارات
                            القرأنية </a>
                    </li>
                    <li class="nav-item" @click.prevent="currentStudentTab = 'courses'">
                        <a class="nav-link" :class="currentStudentTab == 'courses' ? 'active show':'' "
                           id="course-08-tab"
                           href="#course-08" role="tab" aria-controls="course-08" aria-selected="false"><i
                                class="fa fa-check-square-o"></i> الدورات
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade" :class="currentStudentTab == 'profile' ? 'active show':'' "
                         id="profile-08"
                         role="tabpanel" aria-labelledby="profile-08-tab">
                        <div class="user-info">
                            <div class="row">
                                <div class="col-lg-2">
                                    @if ($student->profile_photo ?? null && Storage::disk('users_images')->exists($student->profile_photo))
                                        <img src="{{Storage::disk('users_images')->url($student->profile_photo)}}"
                                             style="width: 100%; height: 60%;"
                                             class="img-fluid mr-15 avatar-small" alt="">
                                    @else
                                        <img src="{{asset('assets/images/teacher.png')}}"
                                             style="width: 100%; height: 60%;"
                                             class="img-fluid mr-15 avatar-small" alt="">
                                    @endif
                                </div>
                                <div class="col-lg-9">
                                    <div class="table-responsive mt-15">
                                        <table class="table table-striped table-hover" style="text-align:center">
                                            <tbody>
                                            <tr>
                                                <th scope="row">الإسم رباعي</th>
                                                <td>{{$student->student_name ?? null}}</td>
                                                <th scope="row">رقم الهوية</th>
                                                <td>{{$student->student_identification_number ?? null}}</td>
                                                <th scope="row">تاريخ الميلاد</th>
                                                <td>{{$student->dob ?? null}}</td>
                                                <th scope="row">رقم الواتساب</th>
                                                <td>{{intval($student->whatsapp_number ?? null)}}</td>
                                            </tr>

                                            <tr>
                                                <th scope="row">اسم المرحلة</th>
                                                <td>{{$student->grade_name ?? null}}</td>
                                                <th scope="row">اسم المحفظ</th>
                                                <td>{{$student->teacher_name ?? null}}</td>
                                            </tr>

                                            <tr>
                                                <th scope="row">اسم ولي الأمر رباعي</th>
                                                <td>{{$student->father_name ?? null}}</td>
                                                <th scope="row">رقم هوية ولي الأمر</th>
                                                <td>{{$student->father_identification_number ?? null}}</td>
                                                <th scope="row">رقم جوال ولي الأمر</th>
                                                <td>{{$student->phone ?? null}}</td>
                                                <th scope="row"></th>
                                                <td></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" :class="currentStudentTab == 'academic' ? 'active show':'' "
                         id="academic-08"
                         role="tabpanel"
                         aria-labelledby="academic-08-tab">
                        <div class="table-responsive mt-15">
                            <table class="table table-striped table-hover" style="text-align:center">
                                <tbody>
                                <tr>
                                    <th scope="row">عدد أجزاء الحفظ</th>
                                    <td>{{$student->total_preservation_parts ?? null}}</td>
                                    <th scope="row">عدد صفحات الحفظ لهذا الشهر</th>
                                    <td>{{$student->number_memorize_pages ?? null}}</td>
                                    <th scope="row">عدد صفحات المراجعة لهذا الشهر</th>
                                    <td>{{$student->number_review_pages ?? null}}</td>
                                    <th scope="row">عدد أيام الحضور لهذا الشهر</th>
                                    <td>{{$student->number_presence_days ?? null}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">عدد أيام الغياب لهذا الشهر</th>
                                    <td>{{$student->number_absence_days ?? null}}</td>
                                    <th scope="row">اسم أخر جزء اختبره</th>
                                    <td>{{$student->last_quran_part ?? null}}</td>
                                    <th scope="row">علامة أخر جزء اختبره</th>
                                    <td>
                                        @if (isset($student->last_exam_mark))
                                            @if ($student->last_exam_mark >= $student->exam_success_mark)
                                                @if ($student->exam_improvement != null && $student->exam_improvement > $student->last_exam_mark)
                                                    <div class="badge-success" style="width: 40px;">
                                                        {{ $student->exam_improvement.'%' }}
                                                    </div>
                                                @else
                                                    <div class="badge-success" style="width: 40px;">
                                                        {{ $student->last_exam_mark.'%' }}
                                                    </div>
                                                @endif
                                            @else
                                                <div class="badge-danger" style="width: 40px;">
                                                    {{ $student->last_exam_mark.'%' }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <th scope="row">حالة الطالب</th>
                                    <td>
                                        @if (isset($student->student_block) && $student->student_block != null)
                                            <div class="badge-danger" style="width: 40px;">
                                                محظور
                                            </div>
                                        @elseif(isset($student->student_warning) && $student->student_warning != null)
                                            <div class="badge-warning" style="width: 50px;">
                                                قيد الحظر
                                            </div>
                                        @else
                                            <div class="badge-success" style="width: 40px;">
                                                منتظم
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" :class="currentStudentTab == 'exams' ? 'active show':'' " id="exams-08"
                         role="tabpanel"
                         aria-labelledby="exams-08-tab">
                        <div class="table-responsive mt-15">
                            <table class="table center-aligned-table mb-0">
                                <thead>
                                <tr class="text-dark table-success">
                                    <th>#</th>
                                    <th>الإسم رباعي</th>
                                    <th>جزء الإختبار</th>
                                    <th>درجة الإختبار</th>
                                    <th>اسم المحفظ</th>
                                    <th>اسم المختبر</th>
                                    <th>تاريخ الإختبار</th>
                                    <th>ملاحظات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($exams as $exam)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $exam->student_name }}</td>
                                        <td>{{ $exam->quran_part_name }}</td>
                                        <td style="text-align: center; align-content: center">
                                            @if ($exam->mark >= $exam->exam_success_mark)
                                                @if ($exam->exam_improvement != null && $exam->exam_improvement > $exam->mark)
                                                    <div class="badge-success" style="width: 40px;">
                                                        {{ $exam->exam_improvement.'%' }}
                                                    </div>
                                                @else
                                                    <div class="badge-success" style="width: 40px;">
                                                        {{ $exam->mark.'%' }}
                                                    </div>
                                                @endif
                                            @else
                                                <div class="badge-danger" style="width: 40px;">
                                                    {{ $exam->mark.'%' }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $exam->teacher_name }}</td>
                                        <td>{{ $exam->tester_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($exam->datetime)->format('Y-m-d') }}</td>
                                        <td>{{ $exam->notes }}</td>
                                    </tr>
                                @empty
                                    <tr style="text-align: center">
                                        <td colspan="7">No data available in table</td>
                                    </tr>
                                @endforelse
                                </tbody>
                                <tfoot>
                                <tr class="text-dark table-success">
                                    <th>#</th>
                                    <th>اسم الطالب</th>
                                    <th>جزء الإختبار</th>
                                    <th>درجة الإختبار</th>
                                    <th>اسم المحفظ</th>
                                    <th>اسم المختبر</th>
                                    <th>تاريخ الإختبار</th>
                                    <th>ملاحظات</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" :class="currentStudentTab == 'courses' ? 'active show':'' "
                         id="course-08"
                         role="tabpanel" aria-labelledby="course-08-tab">
                        <div class="badge-danger">
                            قيد التطوير والبرمجة
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
