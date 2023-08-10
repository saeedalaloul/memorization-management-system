<div class="tab-pane fade" :class="currentStudentTab === 'academic-sunnah' ? 'active show':'' "
     id="academic-sunnah-08"
     role="tabpanel"
     aria-labelledby="academic-sunnah-08-tab">
    <div class="table-responsive mt-15">
        <table class="table table-striped table-hover" style="text-align:center">
            <tbody>
            <tr>
                <th scope="row">اسم الكتاب الحالي</th>
                <td>{{$student->book_name ?? null}}</td>
                <th scope="row">عدد أحاديث الحفظ لهذا الشهر</th>
                <td>
                @if(isset($student->memorize_hadith_from) && isset($student->memorize_hadith_to))
                    {{$student->memorize_hadith_to - $student->memorize_hadith_from}}
                @endif
                <th scope="row">عدد أحاديث المراجعة لهذا الشهر</th>
                <td>
                    @if(isset($student->review_hadith_from) && isset($student->review_hadith_to))
                        {{$student->review_hadith_to - $student->review_hadith_from}}
                    @endif
                </td>
                <th scope="row">عدد أيام الحضور لهذا الشهر</th>
                <td>{{$student->number_presence_days_sunnah ?? null}}</td>
            </tr>
            <tr>
                <th scope="row">عدد أيام الغياب لهذا الشهر</th>
                <td>{{$student->number_absence_days_sunnah ?? null}}</td>
                <th scope="row">اسم أخر جزء اختبره</th>
                <td>{{$student->last_sunnah_part ?? null}}</td>
                <th scope="row">علامة أخر جزء اختبره</th>
                <td>
                    @if (isset($student->last_sunnah_exam_mark))
                        @if ($student->last_sunnah_exam_mark >= $student->sunnah_exam_success_mark)
                            @if ($student->sunnah_exam_improvement != null && $student->sunnah_exam_improvement > $student->last_sunnah_exam_mark)
                                <div class="badge-success" style="width: 40px;">
                                    {{ $student->sunnah_exam_improvement.'%' }}
                                </div>
                            @else
                                <div class="badge-success" style="width: 40px;">
                                    {{ $student->last_sunnah_exam_mark.'%' }}
                                </div>
                            @endif
                        @else
                            <div class="badge-danger" style="width: 40px;">
                                {{ $student->last_sunnah_exam_mark.'%' }}
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
