<table>
    <thead>
    <tr>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">#
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">اسم الطالب
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            colspan="2">بداية الحفظ
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            colspan="2">نهاية الحفظ
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">عدد صفحات الحفظ
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">عدد صفحات المراجعة
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">عدد صفحات مراجعة التجميعي
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">عدد أيام الحضور
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">عدد أيام الغياب
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">عدد أجزاء الحفظ
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">اختبارات التجميعي
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">اختبارات المنفردة
        </th>
    </tr>
    <tr>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            سورة
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            آية
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            سورة
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            أية
        </th>
    </tr>
    </thead>
    <tbody>
    @forelse($reports_monthly_memorization as $report)
        <tr>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{$loop->iteration}}</td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['student_name']))
                    {{$report['student_name']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['sura_start']))
                    {{$report['sura_start']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['aya_from']))
                    {{$report['aya_from']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['sura_end']))
                    {{$report['sura_end']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['aya_to']))
                    {{$report['aya_to']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['number_memorize_pages']))
                    {{round($report['number_memorize_pages'])}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['number_review_pages']))
                    {{round($report['number_review_pages'])}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['number_cumulative_review_pages']))
                    {{round($report['number_cumulative_review_pages'])}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['presence_count']))
                    {{$report['presence_count']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['attendance_count']))
                    {{$report['attendance_count']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['total_preservation_parts']))
                    {{$report['total_preservation_parts']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['exams_deserved']))
                    {{$report['exams_deserved']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['exams_individual']))
                    {{$report['exams_individual']}}
                @endif
            </td>
        </tr>
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
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم الطالب
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            colspan="2">بداية الحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            colspan="2">نهاية الحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد صفحات الحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد صفحات المراجعة
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد صفحات مراجعة التجميعي
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد أيام الحضور
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد أيام الغياب
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد أجزاء الحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اختبارات التجميعي
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اختبارات المنفردة
        </th>
    </tr>
    </tfoot>
</table>
