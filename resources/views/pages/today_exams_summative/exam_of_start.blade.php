<div class="row">
    @include('pages.today_exams_summative.exam_approval')
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="card-body">
                    <div class="tab nav-border">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active show" id="home-02-tab" data-toggle="tab" href="#home-02"
                                   role="tab" aria-controls="home-02"
                                   aria-selected="true">معلومات إختبار التجميعي للطالب</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="home-02" role="tabpanel"
                                 aria-labelledby="home-02-tab">
                                <table class="table table-striped table-hover" style="text-align:center">
                                    <tbody>
                                    <tr class="table-info">
                                        <th scope="row">اسم الطالب</th>
                                        <td class="text-dark">{{$student_name}}</td>
                                        <th scope="row">جزء اختبار التجميعي</th>
                                        <td class="text-dark">{{$quran_part}}</td>
                                        <th scope="row">تاريخ اختبار التجميعي</th>
                                        <td class="text-dark">{{$exam_date}}</td>
                                        <th scope="row">اسم المحفظ</th>
                                        <td class="text-dark">{{$teacher_name}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">اسم المختبر</th>
                                        <td class="text-dark">{{$tester_name}}</td>
                                        <th scope="row">عدد مرات الإعادة</th>
                                        <td class="text-dark">
                                            @if ($numberOfReplays > 0)
                                                <div class="badge-danger" style="width: 80px;">
                                                    {{$numberOfReplays}}
                                                </div>
                                            @else
                                                {{$numberOfReplays}}
                                            @endif
                                        </td>
                                        <th scope="row">عدد أسئلة اختبار التجميعي</th>
                                        <td class="text-dark">{{$exam_questions_count}}</td>
                                        <th scope="row">درجة اختبار التجميعي</th>
                                        <td class="text-dark">{{$exam_mark}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @include('pages.today_exams_summative.form_exam')
            </div>
        </div>
    </div>
</div>
