<div class="col-xl-12 mb-30">
    @php
        if (isset($student_id)) {
         $student = \App\Models\Student::where('id',$student_id)
         ->withSum(['daily_memorization as number_review_pages' => function ($query) {
                        $query->whereMonth('datetime', Date('m'))
                            ->whereYear('datetime', Date('Y'))
                            ->where('type',  App\Models\StudentDailyMemorization::REVIEW_TYPE);
                    }], 'number_pages')
                    ->withSum(['daily_memorization as number_memorize_pages' => function ($query) {
                        $query->whereMonth('datetime', Date('m'))
                            ->whereYear('datetime', Date('Y'))
                            ->where('type',  App\Models\StudentDailyMemorization::MEMORIZE_TYPE);
                    }], 'number_pages')
                    ->withCount(['attendance as attendance_absence_count' => function ($query) {
                        $query->whereMonth('datetime', Date('m'))
                            ->whereYear('datetime',Date('Y'))
                            ->where('status',  App\Models\StudentAttendance::ABSENCE_STATUS);
                    }])
                    ->withCount(['attendance as attendance_presence_count' => function ($query) {
                        $query->whereMonth('datetime', Date('m'))
                            ->whereYear('datetime', Date('Y'))
                            ->where('status', App\Models\StudentAttendance::PRESENCE_STATUS);
                    }])->first();
         $block = $student->student_is_block != null ? true:false;
         $warning = $student->student_is_warning != null ? true:false;
         $user = $student->user;
         $father = $student->father->user;
        }
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
                                    <img src="{{$student->user->profile_photo_url}}" style="width: 100%; height: 60%;"
                                         class="img-fluid mr-15 avatar-small" alt="">
                                </div>
                                <div class="col-lg-9">
                                    <div class="table-responsive mt-15">
                                        <table class="table table-striped table-hover" style="text-align:center">
                                            <tbody>
                                            <tr>
                                                <th scope="row">اسم الطالب</th>
                                                <td>{{$user->name}}</td>
                                                <th scope="row">رقم الهوية</th>
                                                <td>{{$user->identification_number}}</td>
                                                <th scope="row">تاريخ الميلاد</th>
                                                <td>{{$user->dob}}</td>
                                                <th scope="row"></th>
                                                <td></td>
                                            </tr>

                                            <tr>
                                                <th scope="row">اسم المرحلة</th>
                                                <td>{{$student->grade->name}}</td>
                                                <th scope="row">اسم المحفظ</th>
                                                <td>{{$student->group->teacher->user->name}}</td>
                                            </tr>

                                            <tr>
                                                <th scope="row">اسم ولي الأمر</th>
                                                <td>{{$father->name}}</td>
                                                <th scope="row">رقم هوية ولي الأمر</th>
                                                <td>{{$father->identification_number}}</td>
                                                <th scope="row">رقم جوال ولي الأمر</th>
                                                <td>{{$father->phone}}</td>
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
                        @php
                            if (isset($student)){
                                $countOfPartQuran = 0;
                                $quranPartName = "لا يوجد";
                                $exam = $student->exams()->with(['examSuccessMark','quranPart','exam_improvement'])
                                ->whereHas('quranPart', function ($q) {
                                    $q->where('type',\App\Models\QuranPart::INDIVIDUAL_TYPE);
                                })
                                ->orderByDesc('datetime')->first();
                                if ($exam != null) {
                                    $quranPartName = $exam->quranPart->name .' '. $exam->quranPart->description;
                                if ($exam->mark >= $exam->examSuccessMark->mark) {
                                    $countOfPartQuran =  $exam->quranPart->total_preservation_parts;
                                }else{
                                    $countOfPartQuran =  $exam->quranPart->total_preservation_parts - 1;
                                }
                                }
}
                        @endphp
                        <div class="table-responsive mt-15">
                            <table class="table table-striped table-hover" style="text-align:center">
                                <tbody>
                                <tr>
                                    <th scope="row">عدد أجزاء الحفظ</th>
                                    <td>{{$countOfPartQuran}}</td>
                                    <th scope="row">عدد صفحات الحفظ لهذا الشهر</th>
                                    <td>{{$student->number_memorize_pages}}</td>
                                    <th scope="row">عدد صفحات المراجعة لهذا الشهر</th>
                                    <td>{{$student->number_review_pages}}</td>
                                    <th scope="row">عدد أيام الحضور لهذا الشهر</th>
                                    <td>{{$student->attendance_presence_count}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">عدد أيام الغياب لهذا الشهر</th>
                                    <td>{{$student->attendance_absence_count}}</td>
                                    <th scope="row">اسم أخر جزء اختبره</th>
                                    <td>{{$quranPartName}}</td>
                                    <th scope="row">علامة أخر جزء اختبره</th>
                                    <td>
                                        @if ($exam != null)
                                            @if ($exam->mark >= $exam->examSuccessMark->mark)
                                                @if ($exam->exam_improvement != null && $exam->exam_improvement->mark > $exam->mark)
                                                    <div class="badge-success" style="width: 40px;">
                                                        {{ $exam->exam_improvement->mark.'%' }}
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
                                        @endif
                                    </td>
                                    <th scope="row">حالة الطالب</th>
                                    <td>
                                        @if ($block == true)
                                            <div class="badge-danger" style="width: 40px;">
                                                محظور
                                            </div>
                                        @elseif($warning == true)
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
                        @php
                            if (isset($student)) {
                                $exams = [];
                                $exams = $student->exams()->with(['quranPart','examSuccessMark','exam_improvement','teacher.user','tester.user'])->orderByDesc('datetime')->limit(10)->get();
                            }
                        @endphp
                        <div class="table-responsive mt-15">
                            <table class="table center-aligned-table mb-0">
                                <thead>
                                <tr class="text-dark table-success">
                                    <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                        @include('livewire._sort-icon',['field'=>'id'])
                                    </th>
                                    <th>اسم الطالب</th>
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
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $exam->quranPart->name.' '. $exam->quranPart->description }}</td>
                                        <td style="text-align: center; align-content: center">
                                            @if ($exam->mark >= $exam->examSuccessMark->mark)
                                                @if ($exam->exam_improvement != null && $exam->exam_improvement->mark > $exam->mark)
                                                    <div class="badge-success" style="width: 40px;">
                                                        {{ $exam->exam_improvement->mark.'%' }}
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
                                        <td>{{ $exam->teacher->user->name }}</td>
                                        <td>{{ $exam->tester->user->name }}</td>
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
