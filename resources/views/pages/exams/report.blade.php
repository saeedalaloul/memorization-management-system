<table>
    <thead>
    <tr>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            #
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم الطالب
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            جزء الإختبار
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            درجة الإختبار
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم المحفظ
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم المختبر
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            تاريخ الإختبار
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            درجة الإختبار (الخارجية)
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            ملاحظات
        </th>
    </tr>
    </thead>
    <tbody>
    @forelse($exams as $exam)
        <tr>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $loop->iteration }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $exam->student->user->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $exam->QuranPart->name . ' ' . $exam->QuranPart->description }}</td>
            <td style="background-color: {{$exam->mark >= $exam->exam_success_mark->mark ? '#28a745':'#dc3545'}}; color: #FFFFFF;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align: center; vertical-align: center;">
                @if ($exam->exam_improvement != null && $exam->exam_improvement->mark > $exam->mark)
                    {{ $exam->exam_improvement->mark.'%' }}
                @else
                    {{ $exam->mark.'%' }}
                @endif
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $exam->teacher->user->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $exam->tester->user->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ \Carbon\Carbon::parse($exam->datetime)->format('Y-m-d') }}</td>

            @if ($exam->external_exam != null)
                <td style="background-color: {{$exam->mark >= $exam->exam_success_mark->mark ? '#28a745':'#dc3545'}}; color: #FFFFFF;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align: center; vertical-align: center;">
                    <div class="badge-success" style="width: 40px;">
                        {{ $exam->external_exam->mark.'%' }}
                    </div>
                </td>
            @else
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></td>
            @endif
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $exam->notes }}</td>
        </tr>
    @empty
        <tr style="text-align: center">
            <td colspan="9">No data available in table</td>
        </tr>
    @endforelse
    </tbody>
    <tfoot>
    <tr>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            #
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم الطالب
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            جزء الإختبار
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            درجة الإختبار
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم المحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم المختبر
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            تاريخ الإختبار
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            درجة الإختبار (الخارجية)
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            ملاحظات
        </th>
    </tr>
    </tfoot>
</table>
