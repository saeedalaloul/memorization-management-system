<div class="tab-pane fade" :class="currentStudentTab === 'academic' ? 'active show':'' "
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
