<table>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <thead>
    <tr>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            #
        </th>
        {{--        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">--}}
        {{--            معرف الإختبار--}}
        {{--        </th>--}}
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
            درجة الإختبار (الخارجية)
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            تاريخ الإختبار
        </th>
    </tr>
    </thead>
    <tbody>
    @forelse($exams as $exam)
        @if ($exam->quran_part_id != 17 && $exam->quran_part_id != 18)
            <tr>
                <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $loop->iteration }}</td>
                {{--            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $exam->id }}</td>--}}
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $exam->student->user->name }}</td>
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $exam->QuranPart->name . ' ' . $exam->QuranPart->description }}</td>
                <td style="background-color: {{$exam->mark >= $exam->examSuccessMark->mark ? '#28a745':'#dc3545'}}; color: #FFFFFF;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align: center; vertical-align: center;">
                    @if ($exam->exam_improvement != null && $exam->exam_improvement->mark > $exam->mark)
                        {{ $exam->exam_improvement->mark.'%' }}
                    @else
                        {{ $exam->mark.'%' }}
                    @endif
                </td>
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $exam->teacher->user->name }}</td>
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $exam->tester->user->name }}</td>
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                    50
                </td>
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{Date('Y-m-d')}}</td>
            </tr>
        @endif
    @empty
        <tr style="text-align: center">
            <td colspan="8">No data available in table</td>
        </tr>
    @endforelse
    </tbody>
    <tfoot>
    <tr>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            #
        </th>
        {{--        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">--}}
        {{--            معرف الإختبار--}}
        {{--        </th>--}}
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
            درجة الإختبار (الخارجية)
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            تاريخ الإختبار
        </th>
    </tr>
    </tfoot>
</table>
