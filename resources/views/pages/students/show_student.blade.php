<div class="col-xl-12 mb-30">
    @php
        $student =  $this->student[0] ?? null;
        $exams =  $this->student_exams;
        $sunnah_exams =  $this->student_sunnah_exams;
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
                    @if($current_role == \App\Models\User::TEACHER_ROLE && $current_group_type == \App\Models\Group::QURAN_TYPE)
                        <li class="nav-item" @click.prevent="currentStudentTab = 'academic'">
                            <a class="nav-link" :class="currentStudentTab == 'academic' ? 'active show':'' "
                               id="academic-08-tab"
                               href="#academic-08" role="tab" aria-controls="academic-08" aria-selected="false"><i
                                    class="fas fa-group"></i> معلومات التحفيظ قسم (القرآن)
                            </a>
                        </li>
                        <li class="nav-item" @click.prevent="currentStudentTab = 'exams'">
                            <a class="nav-link" :class="currentStudentTab == 'exams' ? 'active show':'' "
                               id="exams-08-tab"
                               href="#exams-08" role="tab" aria-controls="exams-08" aria-selected="false"><i
                                    class="fas fa-book-open"></i>
                                الإختبارات
                                القرأنية </a>
                        </li>
                    @elseif($current_role == \App\Models\User::TEACHER_ROLE && $current_group_type == \App\Models\Group::SUNNAH_TYPE)
                        <li class="nav-item" @click.prevent="currentStudentTab = 'academic-sunnah'">
                            <a class="nav-link" :class="currentStudentTab == 'academic-sunnah' ? 'active show':'' "
                               id="academic-sunnah-08-tab"
                               href="#academic-sunnah-08" role="tab" aria-controls="academic-sunnah-08"
                               aria-selected="false"><i
                                    class="fas fa-group"></i> معلومات التحفيظ قسم (السنة)
                            </a>
                        </li>
                        <li class="nav-item" @click.prevent="currentStudentTab = 'sunnah-exams'">
                            <a class="nav-link" :class="currentStudentTab == 'sunnah-exams' ? 'active show':'' "
                               id="sunnah-exams-08-tab"
                               href="#sunnah-exams-08" role="tab" aria-controls="sunnah-exams-08" aria-selected="false"><i
                                    class="fas fa-book-open"></i>
                                اختبارات
                                السنة </a>
                    @else
                        <li class="nav-item" @click.prevent="currentStudentTab = 'academic'">
                            <a class="nav-link" :class="currentStudentTab == 'academic' ? 'active show':'' "
                               id="academic-08-tab"
                               href="#academic-08" role="tab" aria-controls="academic-08" aria-selected="false"><i
                                    class="fas fa-group"></i> معلومات التحفيظ قسم (القرآن)
                            </a>
                        </li>
                        @if (isset($student->group_sunnah_id))
                            <li class="nav-item" @click.prevent="currentStudentTab = 'academic-sunnah'">
                                <a class="nav-link" :class="currentStudentTab == 'academic-sunnah' ? 'active show':'' "
                                   id="academic-sunnah-08-tab"
                                   href="#academic-sunnah-08" role="tab" aria-controls="academic-sunnah-08"
                                   aria-selected="false"><i
                                        class="fas fa-group"></i> معلومات التحفيظ قسم (السنة)
                                </a>
                            </li>
                        @endif
                        <li class="nav-item" @click.prevent="currentStudentTab = 'exams'">
                            <a class="nav-link" :class="currentStudentTab == 'exams' ? 'active show':'' "
                               id="exams-08-tab"
                               href="#exams-08" role="tab" aria-controls="exams-08" aria-selected="false"><i
                                    class="fas fa-book-open"></i>
                                الإختبارات
                                القرأنية </a>
                        </li>
                        @if (isset($student->group_sunnah_id))
                            <li class="nav-item" @click.prevent="currentStudentTab = 'sunnah-exams'">
                                <a class="nav-link" :class="currentStudentTab == 'sunnah-exams' ? 'active show':'' "
                                   id="sunnah-exams-08-tab"
                                   href="#sunnah-exams-08" role="tab" aria-controls="sunnah-exams-08"
                                   aria-selected="false"><i
                                        class="fas fa-book-open"></i>
                                    اختبارات
                                    السنة </a>
                            </li>
                        @endif
                    @endif
                    {{--                    <li class="nav-item" @click.prevent="currentStudentTab = 'courses'">--}}
                    {{--                        <a class="nav-link" :class="currentStudentTab == 'courses' ? 'active show':'' "--}}
                    {{--                           id="course-08-tab"--}}
                    {{--                           href="#course-08" role="tab" aria-controls="course-08" aria-selected="false"><i--}}
                    {{--                                class="fa fa-check-square-o"></i> الدورات--}}
                    {{--                        </a>--}}
                    {{--                    </li>--}}
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
                                                <th scope="row">الجنس</th>
                                                <td>
                                                    @if (isset($student->student_gender))
                                                        {{\App\Models\User::genders()[$student->student_gender]}}
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <th scope="row">رقم الواتساب</th>
                                                <td>{{intval($student->whatsapp_number ?? null)}}</td>
                                                <th scope="row">اسم المرحلة</th>
                                                <td>{{$student->grade_name ?? null}}</td>
                                                <th scope="row">اسم المحفظ</th>
                                                <td>
                                                    @if ($current_role == \App\Models\User::TEACHER_ROLE)
                                                        @if ($current_group_type == \App\Models\Group::SUNNAH_TYPE)
                                                            {{$student->teacher_sunnah_name ?? null}}
                                                        @else
                                                            {{$student->teacher_name ?? null}}
                                                        @endif
                                                    @else
                                                        {{$student->teacher_name ?? null}}
                                                    @endif
                                                </td>
                                                @if (isset($student->teacher_sunnah_name ) && $current_role != \App\Models\User::TEACHER_ROLE)
                                                    <th scope="row">اسم محفظ السنة</th>
                                                    <td>
                                                        {{$student->teacher_sunnah_name ?? null}}
                                                    </td>
                                                @endif
                                            </tr>

                                            <tr>
                                                <th scope="row">اسم ولي الأمر رباعي</th>
                                                <td>{{$student->father_name ?? null}}</td>
                                                <th scope="row">رقم هوية ولي الأمر</th>
                                                <td>{{$student->father_identification_number ?? null}}</td>
                                                <th scope="row">رقم جوال ولي الأمر</th>
                                                <td>{{$student->phone ?? null}}</td>
                                                <th scope="row">جنس ولى الأمر</th>
                                                <td>
                                                    @if (isset($student->father_gender))
                                                        {{\App\Models\User::genders()[$student->father_gender]}}
                                                    @endif
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($current_role == \App\Models\User::TEACHER_ROLE && $current_group_type == \App\Models\Group::QURAN_TYPE)
                        @include('pages.students.academic_student')
                        @include('pages.students.exam_student')
                    @elseif($current_role == \App\Models\User::TEACHER_ROLE && $current_group_type == \App\Models\Group::SUNNAH_TYPE)
                        @include('pages.students.academic_sunnah_student')
                        @include('pages.students.exam_sunnah_student')
                    @else
                        @include('pages.students.academic_student')
                        @include('pages.students.academic_sunnah_student')
                        @include('pages.students.exam_student')
                        @include('pages.students.exam_sunnah_student')
                    @endif
                    {{--                    <div class="tab-pane fade" :class="currentStudentTab === 'courses' ? 'active show':'' "--}}
                    {{--                         id="course-08"--}}
                    {{--                         role="tabpanel" aria-labelledby="course-08-tab">--}}
                    {{--                        <div class="badge-danger">--}}
                    {{--                            قيد التطوير والبرمجة--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                </div>
            </div>
        </div>
    </div>
</div>
