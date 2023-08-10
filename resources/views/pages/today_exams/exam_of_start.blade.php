<div class="row">
    @include('pages.today_exams.exam_approval')
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="card-body">
                    <div class="tab nav-border">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active show" id="home-02-tab" data-toggle="tab" href="#home-02"
                                   role="tab" aria-controls="home-02"
                                   aria-selected="true">معلومات إختبار الطالب</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="home-02" role="tabpanel"
                                 aria-labelledby="home-02-tab">
                                <table class="table table-striped table-hover" style="text-align:center">
                                    <tbody>
                                    <tr class="table-info">
                                        <th scope="row">اسم الطالب</th>
                                        <td class="text-dark">{{$examOrder->student->user->name}}</td>
                                        <th scope="row">جزء الإختبار</th>
                                        <td class="text-dark">
                                            @if ($examOrder->type == \App\Models\ExamOrder::IMPROVEMENT_TYPE)
                                                <label class="badge badge-success">
                                                    @if($examOrder->partable_type == 'App\Models\QuranPart')
                                                        {{$examOrder->partable->name .' '.$examOrder->partable->description . ' (طلب تحسين درجة)' }}
                                                    @else
                                                        {{$examOrder->partable->name .' ('.$examOrder->partable->total_hadith_parts.') حديث' . ' (طلب تحسين درجة)' }}
                                                    @endif
                                                </label>
                                            @else
                                                @if($examOrder->partable_type == 'App\Models\QuranPart')
                                                    {{$examOrder->partable->name .' '.$examOrder->partable->description }}
                                                @else
                                                    {{$examOrder->partable->name .' ('.$examOrder->partable->total_hadith_parts.') حديث'}}
                                                @endif
                                            @endif
                                        </td>
                                        <th scope="row">تاريخ الإختبار</th>
                                        <td class="text-dark">{{ \Carbon\Carbon::parse($examOrder->datetime)->format('Y-m-d') }}</td>
                                        <th scope="row">اسم المحفظ</th>
                                        <td class="text-dark">{{$examOrder->teacher->user->name}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">اسم المختبر</th>
                                        <td class="text-dark">{{$examOrder->tester->user->name}}</td>
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
                                        <th scope="row">عدد أسئلة الإختبار</th>
                                        <td class="text-dark">{{$exam_questions_count}}</td>
                                        <th scope="row">درجة الإختبار</th>
                                        <td class="text-dark">{{$exam_mark}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @include('pages.today_exams.form_exam')
            </div>
        </div>
    </div>
</div>
