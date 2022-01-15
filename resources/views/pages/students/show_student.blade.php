<div class="col-xl-12 mb-30">
    @php
        if (isset($student_id)) {
         $student = \App\Models\Student::find($student_id);
         $user = $student->user;
         $father = $student->father->user;
         $exam = $student->exams()
         ->orderByDesc('exam_date')->first();
         $countOfPartQuran = 0;
         $quranPartName = "لا يوجد";
         if ($exam != null) {
             if ($exam->calcmarkexam() >= $exam->examSuccessMark->mark) {
             $countOfPartQuran = $exam->quranPart->id - 29;
             $quranPartName = $exam->quranPart->name;
             }else{
             $countOfPartQuran = $exam->quranPart->id - 30;
            }
         }
       $countOfDayAbsence =  $student->attendance()->where('attendance_date', '>=',date('Y-m-1'))
        ->where('attendance_status', '=',0)->count();
         $countOfDayPresence = $student->attendance()->where('attendance_date', '>=',date('Y-m-1'))
        ->where('attendance_status', '=',1)->count();
         $exams = [];
         $exams = $student->exams()->orderByDesc('exam_date')->limit(10)->get();
        }
    @endphp
    <div class="card card-statistics h-100">
        <div class="card-body">
            <h5 class="card-title"> عرض بيانات الطالب</h5>
            <div class="tab round shadow">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{$tab_id == "profile-08"? "active show":""}}" id="profile-08-tab"
                           href="#profile-08" role="tab" wire:click.prevent="update_index_tab('profile-08');"
                           aria-controls="profile-08" aria-selected="true"> <i class="fa fa-user"></i> البيانات
                            الشخصية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab_id == "academic-08"? "active show":""}}" id="academic-08-tab"
                           href="#academic-08" role="tab" wire:click.prevent="update_index_tab('academic-08');"
                           aria-controls="academic-08" aria-selected="false"><i class="fas fa-group"></i> بيانات الحفظ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab_id == "exams-08"? "active show":""}}" id="exams-08-tab"
                           href="#exams-08" role="tab" wire:click.prevent="update_index_tab('exams-08');"
                           aria-controls="exams-08" aria-selected="false"><i class="fas fa-book-open"></i>
                            الإختبارات
                            القرأنية </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab_id == "course-08"? "active show":""}}" id="course-08-tab"
                           href="#course-08" role="tab" wire:click.prevent="update_index_tab('course-08');"
                           aria-controls="course-08" aria-selected="false"><i class="fa fa-check-square-o"></i> الدورات
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade {{$tab_id == "profile-08"? "active show":""}}" id="profile-08"
                         role="tabpanel" aria-labelledby="profile-08-tab">
                        <div class="user-info">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="user-dp pe-3"><img class="m-0"
                                                                   src="{{asset('assets/images/student.png')}}" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-1"></div>
                                <div class="col-lg-9">
                                    <div class="table-responsive mt-15">
                                        <table class="table table-striped table-hover" style="text-align:center">
                                            <tbody>
                                            <tr>
                                                <th scope="row">اسم الطالب</th>
                                                <td>{{$user->name}}</td>
                                                <th scope="row">البريد الإلكتروني</th>
                                                <td>{{$user->email}}</td>
                                                <th scope="row">رقم الهوية</th>
                                                <td>{{$user->identification_number}}</td>
                                                <th scope="row">رقم الجوال</th>
                                                <td>{{$user->phone}}</td>
                                            </tr>

                                            <tr>
                                                <th scope="row">اسم المرحلة</th>
                                                <td>{{$student->grade->name}}</td>
                                                <th scope="row">اسم المحفظ</th>
                                                <td>{{$student->group->teacher->user->name}}</td>
                                                <th scope="row">العنوان</th>
                                                <td>{{$user->address}}</td>
                                                <th scope="row">تاريخ الميلاد</th>
                                                <td>{{$user->dob}}</td>
                                            </tr>

                                            <tr>
                                                <th scope="row">اسم ولي الأمر</th>
                                                <td>{{$father->name}}</td>
                                                <th scope="row">رقم جوال ولي الأمر</th>
                                                <td>{{$father->phone}}</td>
                                                <th scope="row"></th>
                                                <td></td>
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
                    <div class="tab-pane fade {{$tab_id == "academic-08"? "active show":""}}" id="academic-08"
                         role="tabpanel"
                         aria-labelledby="academic-08-tab">
                        <div class="table-responsive mt-15">
                            <table class="table table-striped table-hover" style="text-align:center">
                                <tbody>
                                <tr>
                                    <th scope="row">عدد أجزاء الحفظ</th>
                                    <td>{{$countOfPartQuran}}</td>
                                    <th scope="row">عدد صفحات الحفظ لهذا الشهر</th>
                                    <td>2</td>
                                    <th scope="row">عدد صفحات المراجعة لهذا الشهر</th>
                                    <td>0</td>
                                    <th scope="row">عدد أيام الحضور لهذا الشهر</th>
                                    <td>{{$countOfDayPresence}}</td>
                                </tr>

                                <tr>
                                    <th scope="row">عدد أيام الغياب لهذا الشهر</th>
                                    <td>{{$countOfDayAbsence}}</td>
                                    <th scope="row">اسم أخر جزء اجتازه</th>
                                    <td>{{$quranPartName}}</td>
                                    <th scope="row">علامة أخر جزء اجتازه</th>
                                    <td>
                                        @if ($exam != null)
                                            @if ($exam->calcmarkexam() >= $exam->examSuccessMark->mark)
                                                <div class="badge-success" style="width: 40px;">
                                                    {{ $exam->calcmarkexam().'%' }}
                                                </div>
                                            @else
                                                <div class="badge-danger" style="width: 40px;">
                                                    {{ $exam->calcmarkexam().'%' }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <th scope="row">حالة الطالب</th>
                                    <td> <div class="badge-danger" style="width: 40px;">
                                            مجمد
                                        </div></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade {{$tab_id == "exams-08"? "active show":""}}" id="exams-08" role="tabpanel"
                         aria-labelledby="exams-08-tab">
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
                                        <td>{{ $exam->QuranPart->name }}</td>
                                        <td style="text-align: center; align-content: center">
                                            @if ($exam->calcmarkexam() >= $exam->examSuccessMark->mark)
                                                <div class="badge-success" style="width: 40px;">
                                                    {{ $exam->calcmarkexam().'%' }}
                                                </div>
                                            @else
                                                <div class="badge-danger" style="width: 40px;">
                                                    {{ $exam->calcmarkexam().'%' }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $exam->teacher->user->name }}</td>
                                        <td>{{ $exam->tester->user->name }}</td>
                                        <td>{{ $exam->exam_date }}</td>
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
                    <div class="tab-pane fade {{$tab_id == "course-08"? "active show":""}}" id="course-08"
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
