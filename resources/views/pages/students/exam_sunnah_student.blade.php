<div class="tab-pane fade" :class="currentStudentTab === 'sunnah-exams' ? 'active show':'' " id="sunnah-exams-08"
     role="tabpanel"
     aria-labelledby="sunnah-exams-08-tab">
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
            @forelse($sunnah_exams as $exam)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $exam->student_name ?? '' }}</td>
                    <td>{{ $exam->sunnah_part_name ?? '' }}</td>
                    <td style="text-align: center; align-content: center">
                        @if (isset($exam->mark))
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
                        @endif
                    </td>
                    <td>{{ $exam->teacher_name ?? '' }}</td>
                    <td>{{ $exam->tester_name ?? ''}}</td>
                    <td>{{ \Carbon\Carbon::parse($exam->datetime ?? '')->format('Y-m-d') }}</td>
                    <td>{{ $exam->notes ?? '' }}</td>
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
